<?php

use App\Models\ActivityLog;
use App\Models\Permission;
use App\Models\Role;
use Flux\Flux;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Editar Papel')] class extends Component
{
    #[Locked]
    public Role $role;

    public string $name = '';
    public string $description = '';
    public int $level = 1;
    public array $selectedPermissions = [];

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->authorize('update', $this->role);

        $this->name = $this->role->name;
        $this->description = $this->role->description ?? '';
        $this->level = $this->role->level;
        $this->selectedPermissions = $this->role->permissions->pluck('id')->toArray();
    }

    /**
     * Update the role.
     */
    public function update(): void
    {
        $this->authorize('update', $this->role);

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'level' => ['required', 'integer', 'min:0', 'max:999'],
            'selectedPermissions' => ['array'],
        ]);

        $this->role->update([
            'name' => $this->name,
            'description' => $this->description,
            'level' => $this->level,
        ]);

        $this->role->permissions()->sync($this->selectedPermissions);

        ActivityLog::log(
            action: 'updated',
            description: __('Editou o papel :name', ['name' => $this->role->name]),
            actor: auth()->user(),
            subject: $this->role,
        );

        Flux::toast(variant: 'success', text: __('Papel atualizado com sucesso.'));

        $this->redirect(route('settings.roles.index'), navigate: true);
    }

    /**
     * Get the permissions grouped by group.
     */
    public function with(): array
    {
        return [
            'permissionGroups' => Permission::all()->groupBy('group'),
        ];
    }
};
?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-pages::settings.layout :heading="__('Editar Papel')" :subheading="__('Edite o papel de acesso')">
        <form wire:submit="update" class="mt-6 space-y-6">
            <flux:input wire:model="name" :label="__('Nome')" type="text" required autofocus />

            <flux:input wire:model="description" :label="__('Descrição')" type="text" />

            <flux:input wire:model="level" :label="__('Nível')" type="number" min="0" max="999" required />

            <flux:separator variant="subtle" />

            <flux:heading>{{ __('Permissões') }}</flux:heading>

            @foreach ($permissionGroups as $group => $permissions)
                <div class="space-y-2">
                    <flux:heading size="sm">{{ $group }}</flux:heading>
                    <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($permissions as $permission)
                            <label class="flex items-center gap-2 p-2 border rounded-lg cursor-pointer border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800">
                                <flux:checkbox wire:model="selectedPermissions" value="{{ $permission->id }}" />
                                <span class="text-sm">{{ $permission->description ?: $permission->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit">
                    {{ __('Salvar') }}
                </flux:button>
                <flux:button variant="ghost" href="{{ route('settings.roles.index') }}">
                    {{ __('Cancelar') }}
                </flux:button>
            </div>
        </form>
    </x-pages::settings.layout>
</section>