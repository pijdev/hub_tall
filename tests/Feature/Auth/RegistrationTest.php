<?php

use App\Models\User;

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
});

test('new users can register', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'John',
        'surname' => 'Doe',
        'username' => 'johndoe',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $user = User::where('username', 'johndoe')->first();

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});

test('new users can register without email', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'Jane',
        'surname' => 'Smith',
        'username' => 'janesmith',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $user = User::where('username', 'janesmith')->first();

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();

    expect($user->email)->toBeNull();
});
