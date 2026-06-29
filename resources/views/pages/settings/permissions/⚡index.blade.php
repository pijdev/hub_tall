<?php

use App\Models\ActivityLog;
use App\Models\Permission;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Permissões')] class extends Component
{
    public string $search = '';

    /**
     * Delete a permission.
     */
    public function delete(int $id): void
    {
        $permission = Permission::findOrFail($id);

        $this->authorize('delete', $permission);

        ActivityLog::log(
            action: 'deleted',
            description: __('Excluiu a permissão :name', ['name' => $permission->description ?: $permission->name]),
            actor: auth()->user(),
            subject: $permission,
        );

        $permission->delete();

        Flux::toast(variant: 'success', text: __('Permissão excluída com sucesso.'));
    }

    /**
     * Get the permissions grouped by group.
     */
    public function with(): array
    {
        return [
            'permissionGroups' => Permission::query()
                ->when($this->search, fn ($q) => $q->whereAny(['name', 'description', 'group'], 'like', "%{$this->search}%"))
                ->orderBy('group')
                ->orderBy('name')
                ->get()
                ->groupBy('group'),
        ];
    }
};
?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Permissões') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Permissões')" :subheading="__('Gerencie as permissões do sistema')">
        <div class="mt-6 space-y-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <flux:input wire:model.live="search" placeholder="{{ __('Buscar permissões...') }}" class="w-full sm:max-w-sm" />

                @can('create', App\Models\Permission::class)
                    <flux:button variant="primary" href="{{ route('settings.permissions.create') }}" class="w-full sm:w-auto">
                        {{ __('Nova Permissão') }}
                    </flux:button>
                @endcan
            </div>

            @forelse ($permissionGroups as $group => $permissions)
                <div class="space-y-3">
                    <flux:heading size="lg">{{ $group }}</flux:heading>

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($permissions as $permission)
                            <div class="flex items-center justify-between p-3 border rounded-lg border-zinc-200 dark:border-zinc-700">
                                <div class="space-y-1">
                                    <p class="font-medium text-sm">{{ $permission->description ?: $permission->name }}</p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $permission->name }}</p>
                                </div>

                                <div class="flex items-center gap-1">
                                    @can('update', $permission)
                                        <flux:button variant="ghost" size="sm" icon="pencil" href="{{ route('settings.permissions.edit', $permission) }}" />
                                    @endcan
                                    @can('delete', $permission)
                                        <flux:button
                                            variant="ghost"
                                            size="sm"
                                            icon="trash"
                                            icon:variant="outline"
                                            class="text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/50"
                                            wire:click="delete({{ $permission->id }})"
                                            wire:confirm="{{ __('Tem certeza que deseja excluir esta permissão?') }}"
                                        />
                                    @endcan
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <p class="font-medium">{{ __('Nenhuma permissão encontrada.') }}</p>
                </div>
            @endforelse
        </div>
    </x-pages::settings.layout>
</section>