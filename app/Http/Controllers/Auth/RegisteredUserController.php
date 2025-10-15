<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->numbers(),
                'not_in:123,1234,12345,123456,1234567,12345678,000000,password,qwerty'
            ],
            'g-recaptcha-response' => ['required'],
        ]);

        // Verify reCAPTCHA v3 server-side
        $token = $request->input('g-recaptcha-response');
        $resp = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha.secret'),
            'response' => $token,
        ]);
        $body = $resp->json();

        $minScore = 0.5;
        if( ! ($body['success'] ?? false) || ($body['action'] ?? '') !== 'register' || ($body['score'] ?? 0) < $minScore ){
            return back()->withInput()->withErrors(['recaptcha' => 'reCAPTCHA verification failed.']);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

    // Keep auto-login, but flash a message so the user knows a verification
    // email was sent. This improves UX: after registration they'll be
    // redirected to HOME and see the notification.
    Auth::login($user);

    return redirect(RouteServiceProvider::HOME)
        ->with('status', 'Se ha enviado un enlace de verificación a tu correo. Por favor revisa tu bandeja.');
    }
}
