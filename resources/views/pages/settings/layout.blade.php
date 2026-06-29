<div class="flex items-start max-md:flex-col">
    <div class="me-10 w-full pb-4 md:w-[220px]">
        <flux:navlist aria-label="{{ __('Configurações') }}">
            <flux:navlist.item :href="route('profile.edit')" wire:navigate>{{ __('Perfil') }}</flux:navlist.item>
            <flux:navlist.item :href="route('security.edit')" wire:navigate>{{ __('Segurança') }}</flux:navlist.item>
            <flux:navlist.item :href="route('teams.index')" :current="request()->routeIs('teams.*')" wire:navigate>
                {{ __('Times') }}</flux:navlist.item>
            <flux:navlist.item :href="route('appearance.edit')" wire:navigate>{{ __('Aparência') }}</flux:navlist.item>

            @can('view-branding')
                <flux:navlist.item :href="route('settings.branding')" wire:navigate>{{ __('Marca') }}</flux:navlist.item>
            @endcan


            @can('viewAny', App\Models\User::class)
                <flux:separator class="my-4" />
                <flux:navlist.item :href="route('settings.users.index')" :current="request()->routeIs('settings.users.*')"
                    wire:navigate>{{ __('Usuários') }}</flux:navlist.item>
            @endcan

            @can('viewAny', App\Models\Role::class)
                <flux:navlist.item :href="route('settings.roles.index')" :current="request()->routeIs('settings.roles.*')"
                    wire:navigate>{{ __('Papéis') }}</flux:navlist.item>
            @endcan

            @can('viewAny', App\Models\Permission::class)
                <flux:navlist.item :href="route('settings.permissions.index')"
                    :current="request()->routeIs('settings.permissions.*')" wire:navigate>{{ __('Permissões') }}
                </flux:navlist.item>
            @endcan

            @can('viewAny', App\Models\User::class)
                <flux:separator class="my-4" />
                <flux:navlist.item :href="route('settings.activity-log.index')"
                    :current="request()->routeIs('settings.activity-log.*')" wire:navigate>{{ __('Log de Atividades') }}
                </flux:navlist.item>
            @endcan
        </flux:navlist>
    </div>

    <flux:separator class="md:hidden" />

    <div class="flex-1 self-stretch max-md:pt-6">
        <flux:heading>{{ $heading ?? '' }}</flux:heading>
        <flux:subheading>{{ $subheading ?? '' }}</flux:subheading>

        <div class="mt-5 w-full max-w-lg lg:max-w-3xl">
            {{ $slot }}
        </div>
    </div>
</div>
