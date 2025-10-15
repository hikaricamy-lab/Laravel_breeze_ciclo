<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" id="register-form">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Inline password feedback (no list shown below) -->
        <div class="mt-2">
            <p id="password-feedback" class="mt-2 text-sm text-red-600" aria-live="polite"></p>
        </div>

        <!-- hidden token para reCAPTCHA v3 -->
        <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ml-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

    <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
    <script>
    (function(){
        const form = document.getElementById('register-form');
        const siteKey = "{{ config('services.recaptcha.site_key') }}";
        form.addEventListener('submit', function(e){
            e.preventDefault();
            grecaptcha.ready(function(){
                grecaptcha.execute(siteKey, {action: 'register'}).then(function(token){
                    document.getElementById('g-recaptcha-response').value = token;
                    form.submit();
                });
            });
        });
    })();
    </script>

    <script>
    (function(){
        const form = document.querySelector('form');
        const pwd = document.getElementById('password');
        const pwdConfirmation = document.getElementById('password_confirmation');
        const submitBtn = form ? form.querySelector('button[type="submit"]') : null;
        const feedback = document.getElementById('password-feedback');

        const common = ['123','1234','12345','123456','1234567','12345678','000000','password','qwerty'];

        function setFeedback(text, ok){
            if(!feedback) return;
            feedback.textContent = text;
            feedback.classList.toggle('text-green-600', ok);
            feedback.classList.toggle('text-red-600', !ok);
        }

        function setInputState(inputEl, ok){
            if(!inputEl) return;
            inputEl.classList.toggle('border-green-600', ok);
            inputEl.classList.toggle('border-red-600', !ok);
        }

        function validate(){
            const v = pwd ? pwd.value : '';
            const conf = pwdConfirmation ? pwdConfirmation.value : '';

            const lengthOK = v.length >= 8;
            const upperOK = /[A-Z]/.test(v);
            const lowerOK = /[a-z]/.test(v);
            const numberOK = /[0-9]/.test(v);
            const notCommon = v.length > 0 && !common.includes((v||'').toLowerCase());
            const confirmOK = v && conf && v === conf;

            const missing = [];
            if(!lengthOK) missing.push('mínimo 8 caracteres');
            if(!upperOK) missing.push('una letra mayúscula');
            if(!lowerOK) missing.push('una letra minúscula');
            if(!numberOK) missing.push('un número');
            if(!notCommon) missing.push('no ser una contraseña trivial');
            if(!confirmOK) missing.push('confirmar la contraseña');

            if(missing.length === 0){
                setFeedback('Contraseña válida', true);
                setInputState(pwd, true);
                setInputState(pwdConfirmation, true);
            } else {
                setFeedback('Falta: ' + missing.join(', '), false);
                setInputState(pwd, false);
                setInputState(pwdConfirmation, confirmOK);
            }

            if(submitBtn){
                submitBtn.disabled = (missing.length > 0);
                submitBtn.classList.toggle('opacity-50', missing.length > 0);
                submitBtn.classList.toggle('cursor-not-allowed', missing.length > 0);
            }
        }

        if(pwd) pwd.addEventListener('input', validate);
        if(pwdConfirmation) pwdConfirmation.addEventListener('input', validate);
        validate();
    })();
    </script>
</x-guest-layout>
