<x-layouts::auth :title="__('Cadastro')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Crie uma conta')" :description="__('Preencha os campos abaixo para criar sua conta')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        @if ($teamInvitation)
            <x-team-invitation-alert :invitation="$teamInvitation" :action="__('Cadastre-se')" />
        @endif

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Name -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <flux:input
                    name="name"
                    :label="__('Nome')"
                    :value="old('name')"
                    type="text"
                    required
                    autofocus
                    autocomplete="given-name"
                    :placeholder="__('João')"
                />

                <flux:input
                    name="surname"
                    :label="__('Sobrenome')"
                    :value="old('surname')"
                    type="text"
                    required
                    autocomplete="family-name"
                    :placeholder="__('da Silva')"
                />
            </div>

            <!-- Username -->
            <flux:input
                name="username"
                :label="__('Login')"
                :value="old('username')"
                type="text"
                required
                autocomplete="username"
                placeholder="Login"
            />

            <!-- Email Address (optional) -->
            <flux:input
                name="email"
                :label="__('E-mail (opcional)')"
                :value="old('email')"
                type="email"
                autocomplete="email"
                placeholder="{{ 'email' . '@' . Str::lower(config('app.name')) . '.com' }}"
            />

            <!-- Password -->
            <flux:input
                name="password"
                :label="__('Senha')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Senha')"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />

            <!-- Confirm Password -->
            <flux:input
                name="password_confirmation"
                :label="__('Confirme a senha')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Senha')"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="w-full" data-test="register-user-button">
                    {{ __('Criar Conta') }}
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Já tem uma conta?') }}</span>
            <flux:link
                :href="$teamInvitation ? route('login', ['invitation' => $teamInvitation['code']]) : route('login')"
                data-test="team-invitation-login-link"
                wire:navigate
            >
                {{ __('Entrar') }}
            </flux:link>
        </div>
    </div>
</x-layouts::auth>
