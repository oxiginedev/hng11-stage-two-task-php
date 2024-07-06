<?php

use App\Models\Organisation;
use App\Models\User;

test('user can add another user to an organization', function () {
    $user = User::factory()->create();

    $organisation = Organisation::factory()->create();
    $organisation->members()->attach($user);

    $userToAdd = User::factory()->create();

    // Authenticate the user who will add another user to the organization
    $this->actingAs($user)->postJson('api/organisations/' . $organisation->id . '/users', [
        'userId' => $userToAdd->id,
    ])
        ->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'message' => 'User added to organisation successfully',
        ]);

    // Assert that the user is now associated with the organization
    $this->assertDatabaseHas('organisation_user', [
        'organisation_id' => $organisation->id,
        'user_id' => $userToAdd->id,
    ]);
});
