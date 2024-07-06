<?php

use App\Models\User;

test('user can create an organisation', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('api/organisations', [
        'name' => 'Test Organisation',
        'description' => 'This is a test organisation',
    ])
        ->assertStatus(201)
        ->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'orgId',
                'name',
                'description',
            ],
        ]);

    $this->assertDatabaseHas('organisations', [
        'name' => 'Test Organisation',
        'description' => 'This is a test organisation',
    ]);
});
