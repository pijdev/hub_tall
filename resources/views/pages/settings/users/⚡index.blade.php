<?php

use App\Models\ActivityLog;
use App\Models\User;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Usuários')] class extends Component
{
    public string $search = '';

    /**
     * Delete a user.
     */
    public function delete(int $id): void
    {
        $user = User::findOrFail($id);

        $this->authorize('delete', $user);

        if ($user->isSuperAdmin()) {
            Flux::toast(variant: 'danger', text: __('O Super Admin não pode ser excluído.'));

            return;
        }

        ActivityLog::log(
            action: 'deleted',
            description: __('Excluiu o usuário :name (:email)', ['name' => $user->name, 'email' => $user->email ?? 'sem email']),
            actor: auth()->user(),
            subject: $user,
        );

        $user->delete();

        Flux::toast(variant: 'success', text: __('Usuário excluído com sucesso.'));
    }

    public function with(): array
    {
        return [
            'users' => User::query()
                ->when($this->search, fn ($q) => $q->whereAny(['name', 'surname', 'username', 'email'], 'like', "%{$this->search}%"))
                ->orderBy('name')
                ->paginate(20),
        ];
    }
};
?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Usuários') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Usuários')" :subheading="__('Gerencie os usuários do sistema')">
        <div class="mt-6 space-y-6">
            @can('create', App\Models\User::class)
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <flux:input wire:model.live="search" placeholder="{{ __('Buscar usuários...') }}" class="w-full sm:max-w-sm" />
                    <flux:button variant="primary" href="{{ route('settings.users.create') }}" class="w-full sm:w-auto">
                        {{ __('Novo Usuário') }}
                    </flux:button>
                </div>
            @else
                <flux:input wire:model.live="search" placeholder="{{ __('Buscar usuários...') }}" class="w-full sm:max-w-sm" />
            @endcan

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-zinc-700">
                            <th class="text-left py-3 px-2 font-medium">{{ __('Nome') }}</th>
                            <th class="text-left py-3 px-2 font-medium hidden sm:table-cell">{{ __('Login') }}</th>
                            <th class="text-left py-3 px-2 font-medium hidden md:table-cell">{{ __('E-mail') }}</th>
                            <th class="text-left py-3 px-2 font-medium">{{ __('Status') }}</th>
                            <th class="text-right py-3 px-2 font-medium">{{ __('Ações') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr class="border-b border-zinc-100 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                                <td class="py-3 px-2">
                                    <p class="font-medium">{{ $user->name }} {{ $user->surname }}</p>
                                    @if ($user->isSuperAdmin())
                                        <flux:badge size="sm" color="amber">Super Admin</flux:badge>
                                    @endif
                                </td>
                                <td class="py-3 px-2 text-zinc-500 dark:text-zinc-400 hidden sm:table-cell">{{ $user->username }}</td>
                                <td class="py-3 px-2 text-zinc-500 dark:text-zinc-400 hidden md:table-cell">{{ $user->email ?? '—' }}</td>
                                <td class="py-3 px-2">
                                    <flux:badge size="sm" :color="$user->status === 'active' ? 'emerald' : ($user->status === 'inactive' ? 'red' : 'zinc')">
                                        {{ $user->status }}
                                    </flux:badge>
                                </td>
                                <td class="py-3 px-2 text-right">
                                    @can('update', $user)
                                        <flux:button variant="ghost" size="sm" icon="pencil" href="{{ route('settings.users.edit', $user) }}" />
                                    @endcan
                                    @can('delete', $user)
                                        @if (! $user->isSuperAdmin())
                                            <flux:button
                                                variant="ghost"
                                                size="sm"
                                                icon="trash"
                                                icon:variant="outline"
                                                class="text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/50"
                                                wire:click="delete({{ $user->id }})"
                                                wire:confirm="{{ __('Tem certeza que deseja excluir este usuário?') }}"
                                            />
                                        @endif
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-zinc-500">
                                    {{ __('Nenhum usuário encontrado.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </x-pages::settings.layout>
</section>
