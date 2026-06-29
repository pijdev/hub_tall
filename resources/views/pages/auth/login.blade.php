<x-layouts::auth :title="__('Entrar')">
    <div class="flex flex-col gap-6">
        @php
            $loginMethod = config('fortify.username');
            $isUsernameLogin = $loginMethod === 'username';
        @endphp

        <x-auth-header
            :title="__('Entre com a sua conta')"
            :description="$isUsernameLogin ? __('Informe seu login e senha para entrar no sistema') : __('Informe seu e-mail e senha para entrar no sistema')"
        />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        @if ($teamInvitation)
            <x-team-invitation-alert :invitation="$teamInvitation" :action="__('Entrar')" />
        @endif

        <x-passkey-verify />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Credential Field -->
            @if ($isUsernameLogin)
                <flux:input
                    name="username"
                    :label="__('Login')"
                    :value="old('username')"
                    type="text"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="Login"
                />
            @else
                <flux:input
                    name="email"
                    :label="__('E-mail')"
                    :value="old('email')"
                    type="email"
                    required
                    autofocus
                    autocomplete="email"
                    placeholder="{{ 'email' . '@' . Str::lower(config('app.name')) . '.com' }}"
                />
            @endif

            <!-- Password -->
            <div class="relative">
                <flux:input
                    name="password"
                    :label="__('Senha')"
                    type="password"
                    required
                    autocomplete="current-password"
                    :placeholder="__('Senha')"
                    viewable
                />

                @if (Route::has('password.request'))
                    <flux:link class="absolute top-0 text-sm end-0" :href="route('password.request')" wire:navigate>
                        {{ __('Esqueceu a sua senha?') }}
                    </flux:link>
                @endif
            </div>

            <!-- Remember Me -->
            <flux:checkbox name="remember" :label="__('Lembre de mim')" :checked="old('remember')" />

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full" data-test="login-button">
                    {{ __('Entrar') }}
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Não tem uma conta?') }}</span>
            <flux:link
                :href="$teamInvitation ? route('register', ['invitation' => $teamInvitation['code']]) : route('register')"
                data-test="register-link"
                wire:navigate
            >
                {{ __('Cadastrar') }}
            </flux:link>
        </div>
    </div>
</x-layouts::auth>
