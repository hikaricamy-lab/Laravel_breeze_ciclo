<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\TwoFactorCodeMail;

class TwoFactorController extends Controller
{
    public function index()
    {
        // Sólo mostrar el formulario 2FA si hay un usuario en proceso de verificación
        if (! session()->has('two_factor:user:id')) {
            return redirect()->route('login');
        }

        $user = User::find(session('two_factor:user:id'));
        if (! $user) {
            session()->forget('two_factor:user:id');
            return redirect()->route('login');
        }

        // Enmascarar email para mostrar a dónde se envió el código
        $email = $user->email;
        $masked = preg_replace('/(^.{1}).+(@.+$)/', '$1***$2', $email);

        return view('auth.two-factor', ['email' => $masked]);
    }

    public function store(Request $request)
    {
        $request->validate(['two_factor_code' => 'required']);

    $user = User::find(session('two_factor:user:id'));

        if (!$user || $user->two_factor_code !== $request->two_factor_code) {
            return redirect()->route('two-factor.index')->withErrors(['two_factor_code' => 'Código inválido']);
        }

        if ($user->two_factor_expires_at->lt(now())) {
            return redirect()->route('two-factor.index')->withErrors(['two_factor_code' => 'Código expirado']);
        }

        $user->resetTwoFactorCode();

        // Autenticar y limpiar la sesión de 2FA
        auth()->login($user);

        // Quitar la marca de sesión y regenerar para seguridad
        session()->forget('two_factor:user:id');
        session()->regenerate();

        return redirect()->intended('/dashboard');
    }

    /**
     * Reenviar el código 2FA al email del usuario en sesión.
     */
    public function resend(Request $request)
    {
        if (! session()->has('two_factor:user:id')) {
            return redirect()->route('login');
        }

        $user = User::find(session('two_factor:user:id'));
        if (! $user) {
            session()->forget('two_factor:user:id');
            return redirect()->route('login');
        }

        // Regenerar y guardar código usando el helper del modelo
        if (method_exists($user, 'generateTwoFactorCode')) {
            $user->generateTwoFactorCode();
        } else {
            $user->two_factor_code = rand(100000, 999999);
            $user->two_factor_expires_at = now()->addMinutes(10);
            $user->save();
        }

        // Enviar el correo
        Mail::to($user->email)->send(new TwoFactorCodeMail($user));

        // Mensaje flash para la vista
        $masked = preg_replace('/(^.{1}).+(@.+$)/', '$1***$2', $user->email);
        return back()->with('status', "Código reenviado a $masked");
    }
}