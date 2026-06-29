<?php

use App\Models\ActivityLog;
use App\Models\Role;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Papéis de Acesso')] class extends Component
{
    public string $search = '';

    /**
     * Delete a role.
     */
    public function delete(int $id): void
    {
        $role = Role::findOrFail($id);

        $this->authorize('delete', $role);

        if ($role->name === 'Super Admin') {
            Flux::toast(variant: 'danger', text: __('O papel Super Admin não pode ser excluído.'));

            return;
        }

        ActivityLog::log(
            action: 'deleted',
            description: __('Excluiu o papel :name', ['name' => $role->name]),
            actor: auth()->user(),
            subject: $role,
        );

        $role->delete();

        Flux::toast(variant: 'success', text: __('Papel excluído com sucesso.'));
    }

    /**
     * Get the roles with their permissions.
     */
    public function with(): array
    {
        return [
            'roles' => Role::with('permissions')
                ->when($this->search, fn ($q) => $q->whereAny(['name', 'description'], 'like', "%{$this->search}%"))
                ->orderBy('level', 'desc')
                ->get(),
        ];
    }
};
?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Papéis de Acesso') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Papéis de Acesso')" :subheading="__('Gerencie os papéis e permissões do sistema')">
        <div class="mt-6 space-y-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <flux:input wire:model.live="search" placeholder="{{ __('Buscar papéis...') }}" class="w-full sm:max-w-sm" />

                @can('create', App\Models\Role::class)
                    <flux:button variant="primary" href="{{ route('settings.roles.create') }}" class="w-full sm:w-auto">
                        {{ __('Novo Papel') }}
                    </flux:button>
                @endcan
            </div>

            <div class="space-y-4">
                @forelse ($roles as $role)
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-4 border rounded-lg border-zinc-200 dark:border-zinc-700 gap-3">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <p class="font-medium">{{ $role->name }}</p>
                                <flux:badge size="sm">{{ __('Nível :level', ['level' => $role->level]) }}</flux:badge>
                            </div>
                            @if ($role->description)
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $role->description }}</p>
                            @endif
                            <div class="flex flex-wrap gap-1 mt-2">
                                @foreach ($role->permissions as $permission)
                                    <flux:badge size="sm" variant="pill" color="zinc">{{ $permission->description ?: $permission->name }}</flux:badge>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            @can('update', $role)
                                <flux:button variant="ghost" size="sm" icon="pencil" href="{{ route('settings.roles.edit', $role) }}" />
                            @endcan
                            @can('delete', $role)
                                @if ($role->name !== 'Super Admin')
                                    <flux:button
                                        variant="ghost"
                                        size="sm"
                                        icon="trash"
                                        icon:variant="outline"
                                        class="text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/50"
                                        wire:click="delete({{ $role->id }})"
                                        wire:confirm="{{ __('Tem certeza que deseja excluir este papel?') }}"
                                    />
                                @endif
                            @endcan
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <p class="font-medium">{{ __('Nenhum papel encontrado.') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </x-pages::settings.layout>
</section>