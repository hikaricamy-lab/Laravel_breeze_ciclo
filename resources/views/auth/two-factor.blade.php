<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        Por favor ingresa el código de 6 dígitos que enviamos al correo indicado.
    </div>

    @if(session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">{{ session('status') }}</div>
    @endif

    @if(isset($email))
        <div class="mb-4 text-sm text-gray-700">Código enviado a: <strong>{{ $email }}</strong></div>
    @endif

    <form method="POST" action="{{ route('two-factor.store') }}">
        @csrf
        <div>
            <x-input-label for="two_factor_code" :value="__('Código de verificación')" />
            <x-text-input id="two_factor_code" class="block mt-1 w-full" type="text" name="two_factor_code" required autofocus inputmode="numeric" pattern="\d{6}" maxlength="6" />
            @error('two_factor_code')
                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                Verificar
            </x-primary-button>
        </div>
    </form>

    <form method="POST" action="{{ route('two-factor.resend') }}" class="mt-4">
        @csrf
        <div class="flex items-center justify-between">
            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900">Reenviar código</button>
            <a href="{{ route('login') }}" class="underline text-sm text-gray-600 hover:text-gray-900">Volver al login</a>
        </div>
    </form>
</x-guest-layout>
