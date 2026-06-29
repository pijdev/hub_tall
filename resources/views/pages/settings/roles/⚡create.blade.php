<?php

use App\Models\ActivityLog;
use App\Models\Permission;
use App\Models\Role;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Novo Papel')] class extends Component
{
    public string $name = '';
    public string $description = '';
    public int $level = 1;
    public array $selectedPermissions = [];

    /**
     * Save the role.
     */
    public function save(): void
    {
        $this->authorize('create', Role::class);

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'level' => ['required', 'integer', 'min:0', 'max:999'],
            'selectedPermissions' => ['array'],
        ]);

        $role = Role::create([
            'name' => $this->name,
            'guard_name' => 'web',
            'description' => $this->description,
            'level' => $this->level,
        ]);

        if (! empty($this->selectedPermissions)) {
            $role->permissions()->sync($this->selectedPermissions);
        }

        ActivityLog::log(
            action: 'created',
            description: __('Criou o papel :name', ['name' => $role->name]),
            actor: auth()->user(),
            subject: $role,
        );

        Flux::toast(variant: 'success', text: __('Papel criado com sucesso.'));

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

    <x-pages::settings.layout :heading="__('Novo Papel')" :subheading="__('Crie um novo papel de acesso')">
        <form wire:submit="save" class="mt-6 space-y-6">
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