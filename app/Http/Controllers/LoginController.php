<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LoginController
{
    public function login(Request $request): JsonResponse
    {
        $input = Validator::make($request->input(), [
            'email' => ['required', 'string', 'email:filter', 'exists:users,email'],
            'password' => ['required', 'string', 'max:255'],
        ])->validate();

        if (! $token = auth()->attempt($input)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'data' => [
                'accessToken' => $token,
                'user' => new UserResource(auth()->user()),
            ],
        ]);
    }
}
