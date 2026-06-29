<?php

use App\Models\ActivityLog;
use App\Models\User;
use Flux\Flux;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Editar Usuário')] class extends Component
{
    #[Locked]
    public User $user;

    public string $name = '';
    public string $surname = '';
    public string $username = '';
    public string $email = '';
    public string $phone = '';
    public string $locale = 'pt_BR';
    public string $timezone = 'America/Sao_Paulo';
    public string $status = 'active';

    public function mount(): void
    {
        $this->authorize('update', $this->user);

        $this->name = $this->user->name;
        $this->surname = $this->user->surname ?? '';
        $this->username = $this->user->username;
        $this->email = $this->user->email ?? '';
        $this->phone = $this->user->phone ?? '';
        $this->locale = $this->user->locale ?? 'pt_BR';
        $this->timezone = $this->user->timezone ?? 'America/Sao_Paulo';
        $this->status = $this->user->status ?? 'active';
    }

    public function update(): void
    {
        $this->authorize('update', $this->user);

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:' . User::class . ',username,' . $this->user->id],
            'email' => ['nullable', 'email', 'max:255', 'unique:' . User::class . ',email,' . $this->user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'locale' => ['required', 'string', 'max:10'],
            'timezone' => ['required', 'string', 'max:50'],
            'status' => ['required', 'string', 'in:active,inactive,suspended'],
        ]);

        $this->user->update([
            'name' => $this->name,
            'surname' => $this->surname,
            'username' => $this->username,
            'email' => $this->email ?: null,
            'phone' => $this->phone ?: null,
            'locale' => $this->locale,
            'timezone' => $this->timezone,
            'status' => $this->status,
        ]);

        ActivityLog::log(
            action: 'updated',
            description: __('Editou o usuário :name', ['name' => $this->user->name]),
            actor: auth()->user(),
            subject: $this->user,
        );

        Flux::toast(variant: 'success', text: __('Usuário atualizado com sucesso.'));

        $this->redirect(route('settings.users.index'), navigate: true);
    }
};
?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-pages::settings.layout :heading="__('Editar Usuário')" :subheading="__('Edite os dados do usuário')">
        <form wire:submit="update" class="mt-6 space-y-6">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <flux:input wire:model="name" :label="__('Nome')" type="text" required autofocus />
                <flux:input wire:model="surname" :label="__('Sobrenome')" type="text" required />
            </div>

            <flux:input wire:model="username" :label="__('Login')" type="text" required />

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <flux:input wire:model="email" :label="__('E-mail')" type="email" />
                <flux:input wire:model="phone" :label="__('Telefone')" type="text" />
            </div>

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
                <flux:button variant="primary" type="submit">
                    {{ __('Salvar') }}
                </flux:button>
                <flux:button variant="ghost" href="{{ route('settings.users.index') }}">
                    {{ __('Cancelar') }}
                </flux:button>
            </div>
        </form>
    </x-pages::settings.layout>
</section>