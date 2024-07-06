<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrganisationResource;
use App\Models\Organisation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class OrganisationController
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $organisations = $user->organisations->sortBy('name');

        return response()->json([
            'status' => 'success',
            'message' => 'Organisations retrieved',
            'data' => [
                'organisations' => OrganisationResource::collection($organisations),
            ],
        ]);
    }
    public function store(Request $request): JsonResponse
    {
        $user = auth()->user();

        $input = Validator::make($request->input(), [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ])->validate();

        $organisation = Organisation::create($input);

        // Attach current user to organisation
        $organisation->members()->attach($user->id);

        return response()->json([
            'status' => 'success',
            'message' => 'Organisation created successfully',
            'data' => new OrganisationResource($organisation),
        ], Response::HTTP_CREATED);
    }

    public function show(string $orgId): JsonResponse
    {
        $user = auth()->user();

        $organisation = Organisation::findOrFail($orgId);

        Gate::forUser($user)->authorize('view', $organisation);

        return response()->json([
            'status' => 'success',
            'message' => 'Organisation retrieved',
            'data' => new OrganisationResource($organisation),
        ]);
    }
}
