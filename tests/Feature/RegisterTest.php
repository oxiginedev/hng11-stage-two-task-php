<?php

use App\Models\User;

test('user can create an account and has default organisation', function () {
    $response = $this->postJson('auth/register', [
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'johndoe@gmail.com',
        'phone' => '09088776611',
        'password' => 'password',
    ])
        ->assertCreated()
        ->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'accessToken',
                'user' => [
                    'userId',
                    'firstName',
                    'lastName',
                    'email',
                    'phone',
                ],
            ],
        ]);

    $this->assertAuthenticated();

    $this->assertDatabaseCount('users', 1);
    $this->assertDatabaseHas('organisations', [
        'name' => 'John\'s Organisation',
        'description' => 'Default organisation',
    ]);
});

test('user cannot create account with duplicate email', function () {
    $existingUser = User::factory()->create();

    $response = $this->postJson('auth/register', [
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => $existingUser->email,
        'phone' => '09088776611',
        'password' => 'password',
    ]);

    $this->assertGuest();
});

test('user cannot create account with duplicate phone', function () {
    $existingUser = User::factory()->create();

    $response = $this->postJson('auth/register', [
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'johndoe@gmail.com',
        'phone' => $existingUser->phone,
        'password' => 'password',
    ]);

    $response->assertStatus(422);
});
