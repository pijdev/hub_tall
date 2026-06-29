<?php

use App\Models\User;
use Laravel\Fortify\Features;

beforeEach(function () {
    $this->skipUnlessFortifyHas(Features::twoFactorAuthentication());
});

test('two factor challenge redirects to login when not authenticated', function () {
    $response = $this->get(route('two-factor.login'));

    $response->assertRedirect(route('login'));
});

test('two factor challenge can be rendered', function () {
    Features::twoFactorAuthentication([
        'confirm' => true,
        'confirmPassword' => true,
    ]);

    $user = User::factory()->withTwoFactor()->create();

    $credentialField = config('fortify.username');
    $credentialValue = $credentialField === 'username' ? $user->username : $user->email;

    $this->post(route('login.store'), [
        $credentialField => $credentialValue,
        'password' => 'password',
    ])->assertRedirect(route('two-factor.login'));
});
