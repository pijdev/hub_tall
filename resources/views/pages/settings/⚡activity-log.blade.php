<?php

use App\Models\ActivityLog;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Log de Atividades')] class extends Component
{
    use \Livewire\WithPagination;

    public string $search = '';

    public string $action = '';

    public function with(): array
    {
        return [
            'logs' => ActivityLog::query()
                ->with('actor')
                ->when($this->search, fn ($q) => $q->where('description', 'like', "%{$this->search}%"))
                ->when($this->action, fn ($q) => $q->where('action', $this->action))
                ->latest()
                ->paginate(30),
            'actions' => ActivityLog::query()
                ->select('action')
                ->distinct()
                ->orderBy('action')
                ->pluck('action')
                ->toArray(),
        ];
    }
};
?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Log de Atividades') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Log de Atividades')" :subheading="__('Registro de atividades realizadas no sistema')">
        <div class="mt-6 space-y-6">
            <div class="flex flex-col sm:flex-row gap-4">
                <flux:input wire:model.live="search" placeholder="{{ __('Buscar atividades...') }}" class="sm:max-w-sm" />
                <flux:select wire:model.live="action" class="sm:max-w-xs">
                    <option value="">{{ __('Todas as ações') }}</option>
                    @foreach ($actions as $act)
                        <option value="{{ $act }}">{{ $act }}</option>
                    @endforeach
                </flux:select>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-zinc-700">
                            <th class="text-left py-3 px-2 font-medium">{{ __('Usuário') }}</th>
                            <th class="text-left py-3 px-2 font-medium">{{ __('Ação') }}</th>
                            <th class="text-left py-3 px-2 font-medium">{{ __('Descrição') }}</th>
                            <th class="text-left py-3 px-2 font-medium hidden md:table-cell">{{ __('IP') }}</th>
                            <th class="text-right py-3 px-2 font-medium">{{ __('Data') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr class="border-b border-zinc-100 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                                <td class="py-3 px-2">
                                    @if ($log->actor)
                                        <span class="font-medium">{{ $log->actor->name }}</span>
                                    @else
                                        <span class="text-zinc-500">—</span>
                                    @endif
                                </td>
                                <td class="py-3 px-2">
                                    <flux:badge size="sm" :color="match($log->action) {
                                        'created' => 'emerald',
                                        'updated' => 'blue',
                                        'deleted' => 'red',
                                        default => 'zinc',
                                    }">
                                        {{ $log->action }}
                                    </flux:badge>
                                </td>
                                <td class="py-3 px-2 text-zinc-600 dark:text-zinc-400 max-w-xs truncate">
                                    {{ $log->description }}
                                </td>
                                <td class="py-3 px-2 hidden md:table-cell">
                                    <span class="text-zinc-500 text-xs">{{ $log->ip_address ?? '—' }}</span>
                                </td>
                                <td class="py-3 px-2 text-right text-xs text-zinc-500 whitespace-nowrap">
                                    {{ $log->created_at->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-zinc-500">
                                    {{ __('Nenhuma atividade registrada.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $logs->links() }}
            </div>
        </div>
    </x-pages::settings.layout>
</section>
