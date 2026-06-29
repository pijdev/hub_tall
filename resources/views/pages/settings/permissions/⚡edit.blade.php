<?php

use App\Models\ActivityLog;
use App\Models\Permission;
use Flux\Flux;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Editar Permissão')] class extends Component
{
    #[Locked]
    public Permission $permission;

    public string $name = '';
    public string $description = '';
    public string $group = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->authorize('update', $this->permission);

        $this->name = $this->permission->name;
        $this->description = $this->permission->description ?? '';
        $this->group = $this->permission->group;
    }

    /**
     * Update the permission.
     */
    public function update(): void
    {
        $this->authorize('update', $this->permission);

        $this->validate([
            'name' => ['required', 'string', 'max:255', 'unique:' . Permission::class . ',name,' . $this->permission->id],
            'description' => ['nullable', 'string', 'max:255'],
            'group' => ['required', 'string', 'max:255'],
        ]);

        $this->permission->update([
            'name' => $this->name,
            'description' => $this->description,
            'group' => $this->group,
        ]);

        ActivityLog::log(
            action: 'updated',
            description: __('Editou a permissão :name', ['name' => $this->permission->description ?: $this->permission->name]),
            actor: auth()->user(),
            subject: $this->permission,
        );

        Flux::toast(variant: 'success', text: __('Permissão atualizada com sucesso.'));

        $this->redirect(route('settings.permissions.index'), navigate: true);
    }
};
?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-pages::settings.layout :heading="__('Editar Permissão')" :subheading="__('Edite a permissão de acesso')">
        <form wire:submit="update" class="mt-6 space-y-6">
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