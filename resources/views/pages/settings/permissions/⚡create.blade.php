<?php

use App\Models\ActivityLog;
use App\Models\Permission;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Nova Permissão')] class extends Component
{
    public string $name = '';
    public string $description = '';
    public string $group = '';

    /**
     * Save the permission.
     */
    public function save(): void
    {
        $this->authorize('create', Permission::class);

        $this->validate([
            'name' => ['required', 'string', 'max:255', 'unique:' . Permission::class],
            'description' => ['nullable', 'string', 'max:255'],
            'group' => ['required', 'string', 'max:255'],
        ]);

        $permission = Permission::create([
            'name' => $this->name,
            'guard_name' => 'web',
            'description' => $this->description,
            'group' => $this->group,
        ]);

        ActivityLog::log(
            action: 'created',
            description: __('Criou a permissão :name', ['name' => $permission->description ?: $permission->name]),
            actor: auth()->user(),
            subject: $permission,
        );

        Flux::toast(variant: 'success', text: __('Permissão criada com sucesso.'));

        $this->redirect(route('settings.permissions.index'), navigate: true);
    }
};
?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-pages::settings.layout :heading="__('Nova Permissão')" :subheading="__('Crie uma nova permissão de acesso')">
        <form wire:submit="save" class="mt-6 space-y-6">
            <flux:input wire:model="name" :label="__('Nome (slug)')" type="text" required autofocus />

            <flux:input wire:model="description" :label="__('Descrição')" type="text" />

            <flux:input wire:model="group" :label="__('Grupo')" type="text" required />

            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit">
                    {{ __('Salvar') }}
                </flux:button>
                <flux:button variant="ghost" href="{{ route('settings.permissions.index') }}">
                    {{ __('Cancelar') }}
                </flux:button>
            </div>
        </form>
    </x-pages::settings.layout>
</section>