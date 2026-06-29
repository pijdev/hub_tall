<?php

use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::get('branding-logo', function () {
    // Storage::put() on the 'local' disk saves to storage/app/private/
    $base = storage_path('app/private/branding');

    // Try optimized versions first
    $webp = $base.'/logo.webp';
    $png = $base.'/logo.png';
    $gif = $base.'/logo.gif';

    if (file_exists($webp)) {
        return response()->file($webp, ['Content-Type' => 'image/webp']);
    }

    if (file_exists($png)) {
        return response()->file($png, ['Content-Type' => 'image/png']);
    }

    if (file_exists($gif)) {
        return response()->file($gif, ['Content-Type' => 'image/gif']);
    }

    // Fall back to original unoptimized copy
    $originals = glob($base.'/logo-original.*');
    if (! empty($originals)) {
        return response()->file($originals[0]);
    }

    abort(404);
})->name('branding.logo');

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {
        Route::view('dashboard', 'dashboard')->name('dashboard');
    });

Route::middleware(['auth'])->group(function () {
    Route::livewire('invitations/{invitation}/accept', 'pages::teams.accept-invitation')->name('invitations.accept');
});

require __DIR__.'/settings.php';
