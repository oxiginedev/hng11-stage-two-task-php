<?php

use App\Models\Organisation;
use App\Models\User;

test('user cannot see data from organisation they do not belong to', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $organisation1 = Organisation::factory()->create(['user_id' => $user1->id]);
    $organisation2 = Organisation::factory()->create(['user_id' => $user2->id]);

    expect($user1->allOrganisations()->contains($organisation2))->toBeFalse();
    expect($user2->allOrganisations()->contains($organisation1))->toBeFalse();
});
