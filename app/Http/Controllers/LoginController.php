<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class LoginController
{
    public function login(Request $request): JsonResponse
    {
        $input = Validator::make($request->input(), [
            'email' => ['required', 'string', 'email:filter'],
            'password' => ['required', 'string', 'max:255'],
        ])->validate();

        abort_if(! $token = auth()->attempt($input), 401, 'Authentication failed');

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
