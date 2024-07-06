<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\OrganisationController;
use App\Http\Controllers\OrganisationUserController;
use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/health');

Route::post('/auth/register', [RegisterController::class, 'register']);
Route::post('/auth/login', [LoginController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/api/users/{id}', [OrganisationUserController::class, 'show']);

    Route::post('/api/organisations', [OrganisationController::class, 'store']);
    Route::get('/api/organisations', [OrganisationController::class, 'index']);
    Route::get('/api/organisations/{orgId}', [OrganisationController::class, 'show']);
    Route::post('/api/organisations/{orgId}/users', [OrganisationUserController::class, 'add']);
});
