<?php

use App\Concerns\ProfileValidationRules;
/* @chisel-email-verification */
use Illuminate\Contracts\Auth\MustVerifyEmail;
/* @end-chisel-email-verification */
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Perfil')] class extends Component {
    use ProfileValidationRules;

    public string $name = '';
    public string $surname = '';
    public string $username = '';
    public string $email = '';
    public string $phone = '';
    public string $locale = '';
    public string $timezone = '';
    public string $status = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();

        $this->name = $user->name;
        $this->surname = $user->surname ?? '';
        $this->username = $user->username ?? '';
        $this->email = $user->email ?? '';
        $this->phone = $user->phone ?? '';
        $this->locale = $user->locale ?? 'pt_BR';
        $this->timezone = $user->timezone ?? 'America/Sao_Paulo';
        $this->status = $user->status ?? 'active';
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate($this->profileRules($user->id));

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        Flux::toast(variant: 'success', text: __("Seu perfil foi atualizado, {$user->name}."));

    }

    /* @chisel-email-verification */
    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail && ! Auth::user()->hasVerifiedEmail();
    }

    #[Computed]
    public function showDeleteUser(): bool
    {
        return ! Auth::user() instanceof MustVerifyEmail
            || (Auth::user() instanceof MustVerifyEmail && Auth::user()->hasVerifiedEmail());
    }
    /* @end-chisel-email-verification */
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Configurações de Perfil') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Perfil')" :subheading="__('Atualize seus dados cadastrais')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <flux:input wire:model="name" :label="__('Nome')" type="text" required autofocus autocomplete="given-name" />

                <flux:input wire:model="surname" :label="__('Sobrenome')" type="text" required autocomplete="family-name" />
            </div>

            <flux:input wire:model="username" :label="__('Login')" type="text" required autocomplete="username" />

            <div>
                <flux:input wire:model="email" :label="__('E-mail')" type="email" autocomplete="email" />

                {{-- @chisel-email-verification --}}
                @if ($this->hasUnverifiedEmail)
                    <div>
                        <flux:text class="mt-4">
                            {{ __('Seu e-mail não foi verificado.') }}

                            <flux:link class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                                {{ __('Clique aqui para re-enviar o e-mail de verificação.') }}
                            </flux:link>
                        </flux:text>

                        @if (session('status') === 'verification-link-sent')
                            <flux:text class="mt-2 font-medium !dark:text-green-400 !text-green-600">
                                {{ __('Um novo link de verificação foi enviado para seu endereço de e-mail.') }}
                            </flux:text>
                        @endif
                    </div>
                @endif
                {{-- @end-chisel-email-verification --}}
            </div>

            <flux:input wire:model="phone" :label="__('Telefone')" type="tel" />

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                <flux:select wire:model="locale" :label="__('Idioma')">
                    <option value="pt_BR">Português (Brasil)</option>
                    <option value="en">English</option>
                    <option value="es">Español</option>
                </flux:select>

                <flux:select wire:model="timezone" :label="__('Fuso Horário')">
                    <option value="America/Sao_Paulo">América/São Paulo (UTC-3)</option>
                    <option value="America/Recife">América/Recife (UTC-3)</option>
                    <option value="America/Manaus">América/Manaus (UTC-4)</option>
                    <option value="America/Noronha">América/Noronha (UTC-2)</option>
                    <option value="UTC">UTC</option>
                </flux:select>

                <flux:select wire:model="status" :label="__('Status')">
                    <option value="active">{{ __('Ativo') }}</option>
                    <option value="inactive">{{ __('Inativo') }}</option>
                    <option value="suspended">{{ __('Suspenso') }}</option>
                </flux:select>
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full" data-test="update-profile-button">
                        {{ __('Salvar') }}
                    </flux:button>
                </div>

            </div>
        </form>

        {{-- @chisel-email-verification --}}
        @if ($this->showDeleteUser)
        {{-- @end-chisel-email-verification --}}
            <livewire:pages::settings.delete-user-form />
        {{-- @chisel-email-verification --}}
        @endif
        {{-- @end-chisel-email-verification --}}
    </x-pages::settings.layout>
</section>
