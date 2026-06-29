<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ branding('name') }} &mdash; {{ branding('tagline') }}</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>

<body
    class="font-sans antialiased bg-white dark:bg-zinc-950 text-zinc-800 dark:text-zinc-200 min-h-screen flex flex-col">
    {{-- Header / Nav --}}
    <header
        class="sticky top-0 z-50 backdrop-blur-lg bg-white/70 dark:bg-zinc-950/70 border-b border-zinc-200 dark:border-zinc-800">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 font-bold text-xl tracking-tight">
                    @php
                        $welcomeLogoUrl = branding('logo.url');
                        $welcomeLogoText = branding('logo.text', 'H');
                        $welcomeGradFrom = branding('logo.gradient_from_hex', '#ef4444');
                        $welcomeGradTo = branding('logo.gradient_to_hex', '#f97316');
                    @endphp
                    @if ($welcomeLogoUrl)
                        <img src="{{ $welcomeLogoUrl }}" alt="{{ branding('name') }}"
                            class="size-8 rounded-lg object-cover" />
                    @else
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg text-white text-sm font-bold"
                            style="background: linear-gradient(to bottom right, {{ $welcomeGradFrom }}, {{ $welcomeGradTo }});">{{ $welcomeLogoText }}</span>
                    @endif
                    {{ branding('name') }}
                </a>
                <div class="flex items-center gap-3">


                    <nav class="flex items-center gap-3">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ route('dashboard') }}"
                                    class="inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-medium bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 hover:bg-zinc-700 dark:hover:bg-zinc-200 transition-colors">
                                    {{ __('Dashboard') }}
                                </a>
                            @else
                                <a href="{{ route('login') }}"
                                    class="text-sm font-medium text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 transition-colors">
                                    {{ __('Entrar') }}
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}"
                                        class="inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-medium bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 hover:bg-zinc-700 dark:hover:bg-zinc-200 transition-colors">
                                        {{ __('Cadastrar') }}
                                    </a>
                                @endif
                            @endauth
                        @endif
                    </nav>

                    {{-- Dark Mode --}}
                    <button type="button" x-data
                        @click="$flux.appearance = $flux.appearance === 'dark' ? 'light' : 'dark'"
                        class="flex size-9 items-center justify-center rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors"
                        aria-label="{{ __('Alternar Tema') }}">
                        <svg x-show="$flux.appearance === 'dark'" class="size-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <svg x-show="$flux.appearance !== 'dark'" class="size-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>

                </div>
            </div>
        </div>
    </header>

    {{-- Hero Section --}}
    <section class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-zinc-50 via-white to-white dark:from-zinc-950/20 dark:via-zinc-950 dark:to-zinc-950 pointer-events-none"
            aria-hidden="true"></div>
        <div class="absolute top-0 -right-40 size-[500px] rounded-full opacity-5 blur-3xl pointer-events-none"
            style="background: {{ branding('logo.gradient_from_hex') }};" aria-hidden="true"></div>
        <div class="absolute -bottom-20 -left-40 size-[400px] rounded-full opacity-5 blur-3xl pointer-events-none"
            style="background: {{ branding('logo.gradient_to_hex') }};" aria-hidden="true"></div>

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
            <div class="flex flex-col items-center text-center">
                {{-- Badge --}}
                <div
                    class="inline-flex items-center gap-2 rounded-full border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-4 py-1.5 text-xs font-medium text-zinc-600 dark:text-zinc-400 mb-8">
                    <span class="flex size-2 rounded-full bg-emerald-500"></span>
                    {{ __('Base: Laravel 13 Livewire Starter Kit') }}
                </div>

                {{-- Title --}}
                <h1
                    class="text-5xl sm:text-6xl lg:text-8xl font-black tracking-tight text-zinc-900 dark:text-white leading-none">
                    {{ branding('name') }}
                </h1>
                <p class="mt-3 text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight text-transparent bg-clip-text"
                    style="background-image: linear-gradient(to right, {{ branding('logo.gradient_from_hex') }}, {{ branding('logo.gradient_to_hex') }}); -webkit-background-clip: text; background-clip: text;">
                    {{ branding('tagline') }}
                </p>

                {{-- Description --}}
                <p class="mt-6 max-w-2xl text-lg leading-relaxed text-zinc-600 dark:text-zinc-400">
                    {{ branding('description') }}
                </p>

                {{-- CTAs --}}
                <div class="mt-8 flex flex-wrap justify-center gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}"
                            class="inline-flex items-center justify-center rounded-lg px-6 py-3 text-sm font-semibold bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 hover:bg-zinc-700 dark:hover:bg-zinc-200 transition-colors shadow-lg shadow-zinc-900/10 dark:shadow-white/10">
                            {{ __('Ir para o Dashboard') }}
                            <svg class="ml-2 size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('register') }}"
                            class="inline-flex items-center justify-center rounded-lg px-6 py-3 text-sm font-semibold bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 hover:bg-zinc-700 dark:hover:bg-zinc-200 transition-colors shadow-lg shadow-zinc-900/10 dark:shadow-white/10">
                            {{ __('Crie sua conta') }}
                            <svg class="ml-2 size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </a>
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center justify-center rounded-lg px-6 py-3 text-sm font-semibold border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                            {{ __('Entrar') }}
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </section>

    {{-- Stack Section --}}
    <section class="border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 lg:py-20">
            <div class="text-center max-w-3xl mx-auto mb-12">
                <h2 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">
                    {{ __('Tecnologias') }}
                </h2>
                <p class="mt-4 text-zinc-600 dark:text-zinc-400">
                    {{ __('Construído sobre o melhor ecossistema PHP moderno, com os pacotes oficiais Laravel e ferramentas de ponta.') }}
                </p>
            </div>

            {{-- TALL Stack --}}
            <div class="mb-12">
                <h3
                    class="text-sm font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-500 text-center mb-6">
                    {{ __('Core — TALL Stack') }}
                </h3>
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <a href="https://tailwindcss.com" target="_blank" rel="noopener noreferrer"
                        class="group rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 bg-white dark:bg-zinc-800 hover:shadow-lg hover:shadow-zinc-900/5 dark:hover:shadow-zinc-900/20 transition-all hover:-translate-y-0.5">
                        <div class="flex items-center gap-4">
                            <div
                                class="flex size-12 shrink-0 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30 group-hover:scale-110 transition-transform">
                                <svg class="size-6 text-blue-600 dark:text-blue-400" viewBox="0 0 24 24"
                                    fill="currentColor">
                                    <path
                                        d="M12.001 4.8c-3.2 0-5.2 1.6-6 4.8 1.2-1.6 2.6-2.2 4.2-1.8.913.228 1.565.89 2.288 1.624C13.666 10.618 15.027 12 18.001 12c3.2 0 5.2-1.6 6-4.8-1.2 1.6-2.6 2.2-4.2 1.8-.913-.228-1.565-.89-2.288-1.624C16.337 6.782 14.976 5.4 12.001 4.8zm-6 7.2c-3.2 0-5.2 1.6-6 4.8 1.2-1.6 2.6-2.2 4.2-1.8.913.228 1.565.89 2.288 1.624C7.666 17.818 9.027 19.2 12.001 19.2c3.2 0 5.2-1.6 6-4.8-1.2 1.6-2.6 2.2-4.2 1.8-.913-.228-1.565-.89-2.288-1.624C10.337 13.982 8.976 12.6 6.001 12z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-zinc-900 dark:text-white">Tailwind CSS</p>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">v4</p>
                            </div>
                        </div>
                    </a>

                    <a href="https://alpinejs.dev" target="_blank" rel="noopener noreferrer"
                        class="group rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 bg-white dark:bg-zinc-800 hover:shadow-lg hover:shadow-zinc-900/5 dark:hover:shadow-zinc-900/20 transition-all hover:-translate-y-0.5">
                        <div class="flex items-center gap-4">
                            <div
                                class="flex size-12 shrink-0 items-center justify-center rounded-lg bg-cyan-100 dark:bg-cyan-900/30 group-hover:scale-110 transition-transform">
                                <svg class="size-6 text-cyan-600 dark:text-cyan-400" viewBox="0 0 24 24"
                                    fill="currentColor">
                                    <path
                                        d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm-1-6v2h2v-2h-2zm0-10v8h2V6h-2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-zinc-900 dark:text-white">Alpine.js</p>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                                    {{ __('Reatividade no cliente') }}</p>
                            </div>
                        </div>
                    </a>

                    <a href="https://laravel.com" target="_blank" rel="noopener noreferrer"
                        class="group rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 bg-white dark:bg-zinc-800 hover:shadow-lg hover:shadow-zinc-900/5 dark:hover:shadow-zinc-900/20 transition-all hover:-translate-y-0.5">
                        <div class="flex items-center gap-4">
                            <div
                                class="flex size-12 shrink-0 items-center justify-center rounded-lg bg-red-100 dark:bg-red-900/30 group-hover:scale-110 transition-transform">
                                <svg class="size-6 text-red-600 dark:text-red-400" viewBox="0 0 24 24"
                                    fill="currentColor">
                                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-zinc-900 dark:text-white">Laravel</p>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">v13</p>
                            </div>
                        </div>
                    </a>

                    <a href="https://livewire.laravel.com" target="_blank" rel="noopener noreferrer"
                        class="group rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 bg-white dark:bg-zinc-800 hover:shadow-lg hover:shadow-zinc-900/5 dark:hover:shadow-zinc-900/20 transition-all hover:-translate-y-0.5">
                        <div class="flex items-center gap-4">
                            <div
                                class="flex size-12 shrink-0 items-center justify-center rounded-lg bg-pink-100 dark:bg-pink-900/30 group-hover:scale-110 transition-transform">
                                <svg class="size-6 text-pink-600 dark:text-pink-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-zinc-900 dark:text-white">Livewire</p>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">v4</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            {{-- Pacotes adicionais --}}
            <div>
                <h3
                    class="text-sm font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-500 text-center mb-6">
                    {{ __('Pacotes e Extensões') }}
                </h3>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    {{-- Laravel Fortify --}}
                    <a href="https://laravel.com/docs/13.x/fortify" target="_blank" rel="noopener noreferrer"
                        class="group flex items-center gap-3 rounded-lg border border-zinc-200 dark:border-zinc-700 px-4 py-3 bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                        <span
                            class="flex size-8 shrink-0 items-center justify-center rounded-md bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-xs font-bold">F</span>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-zinc-900 dark:text-white truncate">Laravel Fortify</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 truncate">
                                {{ __('Autenticação backend') }}</p>
                        </div>
                    </a>

                    {{-- Flux UI --}}
                    <a href="https://fluxui.dev" target="_blank" rel="noopener noreferrer"
                        class="group flex items-center gap-3 rounded-lg border border-zinc-200 dark:border-zinc-700 px-4 py-3 bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                        <span
                            class="flex size-8 shrink-0 items-center justify-center rounded-md bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 text-xs font-bold">F</span>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-zinc-900 dark:text-white truncate">Flux UI</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 truncate">v2 — {{ __('Componentes') }}
                            </p>
                        </div>
                    </a>

                    {{-- Laravel AI SDK --}}
                    <a href="https://laravel.com/docs/13.x/ai-sdk" target="_blank" rel="noopener noreferrer"
                        class="group flex items-center gap-3 rounded-lg border border-zinc-200 dark:border-zinc-700 px-4 py-3 bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                        <span
                            class="flex size-8 shrink-0 items-center justify-center rounded-md bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 text-xs font-bold">AI</span>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-zinc-900 dark:text-white truncate">Laravel AI SDK</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 truncate">v0.8 —
                                {{ __('Provedores de IA') }}</p>
                        </div>
                    </a>

                    {{-- Laravel Passkeys --}}
                    <a href="https://laravel.com/docs/13.x/passkeys" target="_blank" rel="noopener noreferrer"
                        class="group flex items-center gap-3 rounded-lg border border-zinc-200 dark:border-zinc-700 px-4 py-3 bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                        <span
                            class="flex size-8 shrink-0 items-center justify-center rounded-md bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 text-xs font-bold">PK</span>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-zinc-900 dark:text-white truncate">Laravel Passkeys</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 truncate">
                                {{ __('WebAuthn sem senha') }}</p>
                        </div>
                    </a>

                    {{-- Blaze --}}
                    <a href="https://livewire.laravel.com/docs/blaze" target="_blank" rel="noopener noreferrer"
                        class="group flex items-center gap-3 rounded-lg border border-zinc-200 dark:border-zinc-700 px-4 py-3 bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                        <span
                            class="flex size-8 shrink-0 items-center justify-center rounded-md bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 text-xs font-bold">B</span>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-zinc-900 dark:text-white truncate">Blaze</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 truncate">{{ __('Otimização Blade') }}
                            </p>
                        </div>
                    </a>

                    {{-- Laravel Tinker --}}
                    <a href="https://laravel.com/docs/13.x/artisan#tinker" target="_blank" rel="noopener noreferrer"
                        class="group flex items-center gap-3 rounded-lg border border-zinc-200 dark:border-zinc-700 px-4 py-3 bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                        <span
                            class="flex size-8 shrink-0 items-center justify-center rounded-md bg-sky-100 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 text-xs font-bold">T</span>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-zinc-900 dark:text-white truncate">Laravel Tinker</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 truncate">{{ __('REPL interativo') }}
                            </p>
                        </div>
                    </a>

                    {{-- Laravel Pail --}}
                    <a href="https://laravel.com/docs/13.x/pail" target="_blank" rel="noopener noreferrer"
                        class="group flex items-center gap-3 rounded-lg border border-zinc-200 dark:border-zinc-700 px-4 py-3 bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                        <span
                            class="flex size-8 shrink-0 items-center justify-center rounded-md bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 text-xs font-bold">P</span>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-zinc-900 dark:text-white truncate">Laravel Pail</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 truncate">{{ __('Logs no terminal') }}
                            </p>
                        </div>
                    </a>

                    {{-- Laravel Pint --}}
                    <a href="https://laravel.com/docs/13.x/pint" target="_blank" rel="noopener noreferrer"
                        class="group flex items-center gap-3 rounded-lg border border-zinc-200 dark:border-zinc-700 px-4 py-3 bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                        <span
                            class="flex size-8 shrink-0 items-center justify-center rounded-md bg-teal-100 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400 text-xs font-bold">P</span>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-zinc-900 dark:text-white truncate">Laravel Pint</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 truncate">
                                {{ __('Formatador de código') }}</p>
                        </div>
                    </a>

                    {{-- Larastan --}}
                    <a href="https://larastan.dev" target="_blank" rel="noopener noreferrer"
                        class="group flex items-center gap-3 rounded-lg border border-zinc-200 dark:border-zinc-700 px-4 py-3 bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                        <span
                            class="flex size-8 shrink-0 items-center justify-center rounded-md bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs font-bold">L</span>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-zinc-900 dark:text-white truncate">Larastan</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 truncate">PHPStan
                                {{ __('para Laravel') }}</p>
                        </div>
                    </a>

                    {{-- Pest --}}
                    <a href="https://pestphp.com" target="_blank" rel="noopener noreferrer"
                        class="group flex items-center gap-3 rounded-lg border border-zinc-200 dark:border-zinc-700 px-4 py-3 bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                        <span
                            class="flex size-8 shrink-0 items-center justify-center rounded-md bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 text-xs font-bold">P</span>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-zinc-900 dark:text-white truncate">Pest PHP</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 truncate">v4 —
                                {{ __('Testes elegantes') }}</p>
                        </div>
                    </a>

                    {{-- Sail --}}
                    <a href="https://laravel.com/docs/13.x/sail" target="_blank" rel="noopener noreferrer"
                        class="group flex items-center gap-3 rounded-lg border border-zinc-200 dark:border-zinc-700 px-4 py-3 bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                        <span
                            class="flex size-8 shrink-0 items-center justify-center rounded-md bg-cyan-100 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400 text-xs font-bold">S</span>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-zinc-900 dark:text-white truncate">Laravel Sail</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 truncate">Docker</p>
                        </div>
                    </a>

                    {{-- League CommonMark --}}
                    <a href="https://commonmark.thephpleague.com" target="_blank" rel="noopener noreferrer"
                        class="group flex items-center gap-3 rounded-lg border border-zinc-200 dark:border-zinc-700 px-4 py-3 bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                        <span
                            class="flex size-8 shrink-0 items-center justify-center rounded-md bg-gray-100 dark:bg-gray-900/30 text-gray-600 dark:text-gray-400 text-xs font-bold">M</span>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-zinc-900 dark:text-white truncate">CommonMark</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 truncate">{{ __('Markdown para PHP') }}
                            </p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- AI Providers Section --}}
    <section class="border-t border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 lg:py-20">
            <div class="text-center max-w-3xl mx-auto mb-10">
                <h2 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">
                    {{ __('Provedores de IA Compatíveis') }}
                </h2>
                <p class="mt-4 text-zinc-600 dark:text-zinc-400">
                    {{ __('Graças ao Laravel AI SDK, o HUB suporta dezenas de provedores de inteligência artificial.') }}
                </p>
            </div>

            <div class="flex flex-wrap justify-center gap-3">
                @php
                    $providers = [
                        [
                            'name' => 'OpenAI',
                            'url' => 'https://openai.com',
                            'color' => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400',
                        ],
                        [
                            'name' => 'Anthropic',
                            'url' => 'https://anthropic.com',
                            'color' => 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400',
                        ],
                        [
                            'name' => 'Gemini',
                            'url' => 'https://ai.google.dev',
                            'color' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400',
                        ],
                        [
                            'name' => 'Azure OpenAI',
                            'url' => 'https://azure.microsoft.com',
                            'color' => 'bg-sky-100 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400',
                        ],
                        [
                            'name' => 'AWS Bedrock',
                            'url' => 'https://aws.amazon.com/bedrock',
                            'color' => 'bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400',
                        ],
                        [
                            'name' => 'Groq',
                            'url' => 'https://groq.com',
                            'color' => 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400',
                        ],
                        [
                            'name' => 'xAI',
                            'url' => 'https://x.ai',
                            'color' => 'bg-zinc-100 dark:bg-zinc-800/50 text-zinc-600 dark:text-zinc-400',
                        ],
                        [
                            'name' => 'DeepSeek',
                            'url' => 'https://deepseek.com',
                            'color' => 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400',
                        ],
                        [
                            'name' => 'Mistral',
                            'url' => 'https://mistral.ai',
                            'color' => 'bg-cyan-100 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400',
                        ],
                        [
                            'name' => 'Ollama',
                            'url' => 'https://ollama.ai',
                            'color' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400',
                        ],
                        [
                            'name' => 'ElevenLabs',
                            'url' => 'https://elevenlabs.io',
                            'color' => 'bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400',
                        ],
                        [
                            'name' => 'Cohere',
                            'url' => 'https://cohere.com',
                            'color' => 'bg-teal-100 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400',
                        ],
                        [
                            'name' => 'Jina',
                            'url' => 'https://jina.ai',
                            'color' => 'bg-violet-100 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400',
                        ],
                        [
                            'name' => 'VoyageAI',
                            'url' => 'https://voyageai.com',
                            'color' => 'bg-lime-100 dark:bg-lime-900/30 text-lime-600 dark:text-lime-400',
                        ],
                        [
                            'name' => 'OpenRouter',
                            'url' => 'https://openrouter.ai',
                            'color' => 'bg-slate-100 dark:bg-slate-900/30 text-slate-600 dark:text-slate-400',
                        ],
                    ];
                @endphp

                @foreach ($providers as $p)
                    <a href="{{ $p['url'] }}" target="_blank" rel="noopener noreferrer"
                        class="inline-flex items-center gap-2 rounded-full border border-zinc-200 dark:border-zinc-700 px-4 py-2 text-xs font-medium {{ $p['color'] }} hover:bg-zinc-100 dark:hover:bg-zinc-700/50 transition-colors">
                        {{ $p['name'] }}
                        <svg class="size-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Features Section --}}
    <section class="border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 lg:py-20">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <h2 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">
                    {{ __('Recursos Inclusos') }}
                </h2>
                <p class="mt-4 text-zinc-600 dark:text-zinc-400">
                    {{ __('Tudo que você precisa para começar seu próximo grande sistema.') }}
                </p>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                {{-- Auth --}}
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 bg-white dark:bg-zinc-800">
                    <div
                        class="flex size-10 items-center justify-center rounded-lg bg-indigo-100 dark:bg-indigo-900/30 mb-4">
                        <svg class="size-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ __('Autenticação Completa') }}
                    </h3>
                    <ul class="mt-3 space-y-1.5 text-sm text-zinc-600 dark:text-zinc-400">
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 size-4 shrink-0 text-emerald-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            {{ __('Login com username ou e-mail') }}
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 size-4 shrink-0 text-emerald-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            {{ __('Registro e verificação de e-mail') }}
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 size-4 shrink-0 text-emerald-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            {{ __('Redefinição de senha') }}
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 size-4 shrink-0 text-emerald-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            {{ __('Autenticação em duas etapas (2FA/TOTP)') }}
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 size-4 shrink-0 text-emerald-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            {{ __('Passkeys (WebAuthn — acesso sem senha)') }}
                        </li>
                    </ul>
                </div>

                {{-- Teams & Permissions --}}
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 bg-white dark:bg-zinc-800">
                    <div
                        class="flex size-10 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30 mb-4">
                        <svg class="size-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ __('Times e Permissões') }}
                    </h3>
                    <ul class="mt-3 space-y-1.5 text-sm text-zinc-600 dark:text-zinc-400">
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 size-4 shrink-0 text-emerald-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            {{ __('Múltiplos times por usuário') }}
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 size-4 shrink-0 text-emerald-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            {{ __('Papéis e permissões granulares') }}
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 size-4 shrink-0 text-emerald-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            {{ __('Convites para times') }}
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 size-4 shrink-0 text-emerald-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            {{ __('Super Admin com acesso total') }}
                        </li>
                    </ul>
                </div>

                {{-- Admin & Security --}}
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 bg-white dark:bg-zinc-800">
                    <div
                        class="flex size-10 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-900/30 mb-4">
                        <svg class="size-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ __('Administração') }}</h3>
                    <ul class="mt-3 space-y-1.5 text-sm text-zinc-600 dark:text-zinc-400">
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 size-4 shrink-0 text-emerald-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            {{ __('CRUD de usuários completo') }}
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 size-4 shrink-0 text-emerald-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            {{ __('Log de atividades') }}
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 size-4 shrink-0 text-emerald-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            {{ __('Dashboard com métricas') }}
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="mt-0.5 size-4 shrink-0 text-emerald-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            {{ __('Hash de senha com Argon2id') }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="border-t border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 lg:py-20 text-center">
            <h2 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">
                {{ __('Pronto para construir seu próximo grande sistema?') }}
            </h2>
            <p class="mt-4 text-lg text-zinc-600 dark:text-zinc-400 max-w-xl mx-auto">
                {{ __('O HUB é a base que você precisa. Crie sua conta e explore todos os recursos.') }}
            </p>
            <div class="mt-8">
                @auth
                    <a href="{{ route('dashboard') }}"
                        class="inline-flex items-center justify-center rounded-lg px-6 py-3 text-sm font-semibold bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 hover:bg-zinc-700 dark:hover:bg-zinc-200 transition-colors shadow-lg">
                        {{ __('Ir para o Dashboard') }}
                    </a>
                @else
                    <a href="{{ route('register') }}"
                        class="inline-flex items-center justify-center rounded-lg px-6 py-3 text-sm font-semibold bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 hover:bg-zinc-700 dark:hover:bg-zinc-200 transition-colors shadow-lg">
                        {{ __('Criar conta gratuita') }}
                        <svg class="ml-2 size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                @endauth
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
            <div
                class="flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-zinc-500 dark:text-zinc-500">
                <p>&copy; {{ date('Y') }} {{ branding('name') }}.
                    {{ __('Todos os direitos reservados.') }}</p>
                <div class="flex items-center gap-2">
                    <span>{{ __('Feito com') }}</span>
                    <span class="text-red-500">&hearts;</span>
                    <span>{{ __('no Brasil') }}</span>
                    <span class="mx-2">&middot;</span>
                    <span>PHP {{ phpversion() }}</span>
                    <span class="mx-2">&middot;</span>
                    <a href="https://laravel.com" target="_blank" rel="noopener noreferrer"
                        class="hover:text-zinc-800 dark:hover:text-zinc-300 transition-colors">Laravel
                        {{ app()->version() }}</a>
                </div>
            </div>
        </div>
    </footer>

    @fluxScripts
</body>

</html>
