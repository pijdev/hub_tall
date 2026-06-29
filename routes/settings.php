<?php

use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::livewire('settings/profile', 'pages::settings.profile')->name('profile.edit');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('settings/appearance', 'pages::settings.appearance')->name('appearance.edit');

    Route::livewire('settings/branding', 'pages::settings.branding')
        ->middleware('permission:view-branding')
        ->name('settings.branding');

    Route::livewire('settings/roles', 'pages::settings.roles.index')
        ->middleware('permission:view-roles')
        ->name('settings.roles.index');

    Route::livewire('settings/roles/create', 'pages::settings.roles.create')
        ->middleware('permission:create-roles')
        ->name('settings.roles.create');

    Route::livewire('settings/roles/{role}', 'pages::settings.roles.edit')
        ->middleware('permission:edit-roles')
        ->name('settings.roles.edit');

    Route::livewire('settings/permissions', 'pages::settings.permissions.index')
        ->middleware('permission:view-permissions')
        ->name('settings.permissions.index');

    Route::livewire('settings/permissions/create', 'pages::settings.permissions.create')
        ->middleware('permission:create-permissions')
        ->name('settings.permissions.create');

    Route::livewire('settings/permissions/{permission}', 'pages::settings.permissions.edit')
        ->middleware('permission:edit-permissions')
        ->name('settings.permissions.edit');

    Route::livewire('settings/users', 'pages::settings.users.index')
        ->middleware('permission:view-users')
        ->name('settings.users.index');

    Route::livewire('settings/users/create', 'pages::settings.users.create')
        ->middleware('permission:create-users')
        ->name('settings.users.create');

    Route::livewire('settings/users/{user}', 'pages::settings.users.edit')
        ->middleware('permission:edit-users')
        ->name('settings.users.edit');

    Route::livewire('settings/activity-log', 'pages::settings.activity-log')
        ->middleware('permission:view-users')
        ->name('settings.activity-log.index');

    Route::livewire('settings/security', 'pages::settings.security')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('security.edit');

    Route::livewire('settings/teams', 'pages::teams.index')->name('teams.index');

    Route::middleware(EnsureTeamMembership::class)->group(function () {
        Route::livewire('settings/teams/{team}', 'pages::teams.edit')->name('teams.edit');
    });
});

Route::get('.well-known/passkey-endpoints', function () {
    return response()->json([
        'enroll' => route('security.edit'),
        'manage' => route('security.edit'),
    ]);
})->name('well-known.passkeys');
