<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class RegisterController
{
    public function register(Request $request): JsonResponse
    {
        $input = Validator::make($request->input(), [
            'firstName' => ['required', 'string', 'max:255'],
            'lastName' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:filter', 'unique:users,email'],
            'phone' => ['required', 'digits:11', 'unique:users,phone'],
            'password' => ['required', Password::defaults()],
        ])->validate();

        $user = DB::transaction(function () use ($input) {
            $user = User::create([
                'first_name' => $input['firstName'],
                'last_name' => $input['lastName'],
                'email' => $input['email'],
                'phone' => $input['phone'],
                'password' => $input['password'],
            ]);

            $organisation = Organisation::create([
                'name' => $user->first_name . '\'s Organisation',
                'description' => 'Default organisation',
            ]);

            $organisation->members()->attach($user);

            return $user;
        });

        $token = auth()->login($user);

        return response()->json([
            'status' => 'success',
            'message' => 'Registration successful',
            'data' => [
                'accessToken' => $token,
                'user' => new UserResource($user),
            ],
        ], 201);
    }
}
