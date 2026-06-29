@php
    use Illuminate\Support\Facades\DB;

    $user = auth()->user();

    // 1. Usuários logados (com sessão ativa)
    $loggedInUserIds = DB::table('sessions')->whereNotNull('user_id')->distinct()->pluck('user_id');

    $loggedUsersCount = $loggedInUserIds->count();

    // 2. Times ativos (com pelo menos um membro logado)
    $activeTeamsCount = 0;
    if ($loggedUsersCount > 0) {
        $activeTeamsCount = DB::table('team_members')
            ->whereIn('user_id', $loggedInUserIds)
            ->distinct('team_id')
            ->count('team_id');
    }

    // 3. Status do servidor
    $dbConnected = false;
    try {
        DB::connection()->getPdo();
        $dbConnected = true;
    } catch (\Exception $e) {
        $dbConnected = false;
    }
    $serverOnline = $dbConnected;

    // 4. README.md content
    $readmePath = base_path('README.md');
    $readmeContent = '';
    if (file_exists($readmePath)) {
        $readmeContent = file_get_contents($readmePath);
        $converter = new \League\CommonMark\CommonMarkConverter([
            'html_input' => 'escape',
            'allow_unsafe_links' => false,
        ]);
        $readmeHtml = $converter->convert($readmeContent);
    }
@endphp

<x-layouts::app :title="__('Início')">
    <livewire:pages::teams.pending-invitations-modal />

    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl p-4 md:p-6">
        <div>
            <flux:heading size="xl">{{ __('Bem-vindo, :name!', ['name' => $user->name]) }}</flux:heading>
            <flux:subheading>{{ __('Resumo do sistema') }}</flux:subheading>
        </div>

        {{-- Stats Cards --}}
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            {{-- Card: Usuários Logados --}}
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-5 bg-white dark:bg-zinc-800/50">
                <div class="flex items-center gap-3">
                    <div class="flex size-10 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30">
                        <flux:icon.users class="size-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <p class="text-2xl font-bold">{{ $loggedUsersCount }}</p>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Usuários Logados') }}</p>
                    </div>
                </div>
            </div>

            {{-- Card: Times Ativos --}}
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-5 bg-white dark:bg-zinc-800/50">
                <div class="flex items-center gap-3">
                    <div
                        class="flex size-10 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/30">
                        <flux:icon.folder class="size-5 text-emerald-600 dark:text-emerald-400" />
                    </div>
                    <div>
                        <p class="text-2xl font-bold">{{ $activeTeamsCount }}</p>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Times Ativos') }}</p>
                    </div>
                </div>
            </div>

            {{-- Card: Servidor --}}
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-5 bg-white dark:bg-zinc-800/50">
                <div class="flex items-center gap-3">
                    <div
                        class="flex size-10 items-center justify-center rounded-lg {{ $serverOnline ? 'bg-green-100 dark:bg-green-900/30' : 'bg-red-100 dark:bg-red-900/30' }}">
                        <flux:icon.cloud
                            class="size-5 {{ $serverOnline ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}" />
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span
                                class="inline-flex size-2.5 rounded-full {{ $serverOnline ? 'bg-green-500' : 'bg-red-500' }}"></span>
                            <p class="text-2xl font-bold">{{ $serverOnline ? __('Online') : __('Offline') }}</p>
                        </div>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Servidor') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- README.md --}}
        @if (!empty($readmeHtml))
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-6 bg-white dark:bg-zinc-800/50">
                <div class="prose prose-sm dark:prose-invert max-w-none">
                    {!! $readmeHtml !!}
                </div>
            </div>
        @endif
    </div>
</x-layouts::app>
