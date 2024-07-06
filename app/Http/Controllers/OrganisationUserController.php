<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class OrganisationUserController
{
    public function show(string $userId): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $otherUser = User::findOrFail($userId);

        $allowed = $user->organisations->contains(function ($organisation) use ($otherUser) {
            return $organisation->hasUserWithId($otherUser->id);
        });

        abort_unless($allowed, Response::HTTP_NOT_FOUND, 'User not found');

        return response()->json([
            'status' => 'success',
            'message' => 'User profile retrieved',
            'data' => new UserResource($otherUser),
        ]);
    }

    /**
     * Add a user to an organisation
     */
    public function add(Request $request, string $orgId)
    {
        $user = auth()->user();

        $organisation = Organisation::findOrFail($orgId);

        Gate::forUser($user)->authorize('addUser', $organisation);

        $input = Validator::make($request->input(), [
            'userId' => ['required', 'uuid', 'exists:users,id'],
        ])->after(
            $this->ensureUserIsNotAlreadyInOrg($organisation, $request->userId),
        )->validate();

        $newMember = User::findOrFail($input['userId']);

        $organisation->members()->attach($newMember);

        return response()->json([
            'status' => 'success',
            'message' => 'User added to organisation successfully',
        ]);
    }

    /**
     * Ensure user is not in organisation already
     */
    protected function ensureUserIsNotAlreadyInOrg(Organisation $organisation, $userId)
    {
        return function ($validator) use ($organisation, $userId) {
            $validator->errors()->addIf(
                $organisation->hasUserWithId($userId),
                'userId',
                __('This user already belongs to organisation'),
            );
        };
    }
}
