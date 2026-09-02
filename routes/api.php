<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DirectoryController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\EventController;

/*
|--------------------------------------------------------------------------
| API Routes - KITA Mobile RESTful API
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Rutas Públicas de la App Móvil
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

Route::prefix('public')->group(function () {
    Route::get('/directory', [DirectoryController::class, 'index']);
    Route::get('/profiles/{slug}', [DirectoryController::class, 'show']);
});

// Rutas Privadas del Dashboard Móvil (Protegidas por Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::get('/dashboard/profile', [ProfileController::class, 'show']);
    Route::put('/dashboard/profile', [ProfileController::class, 'update']);
    Route::post('/dashboard/profile/photo', [ProfileController::class, 'uploadPhoto']);
    Route::post('/dashboard/profile/switch/{id}', [ProfileController::class, 'switchProfile']);
    Route::post('/dashboard/profile/create', [ProfileController::class, 'store']);
    Route::delete('/dashboard/profile/{id}', [ProfileController::class, 'destroy']);

    Route::get('/dashboard/media', [MediaController::class, 'index']);
    Route::post('/dashboard/media', [MediaController::class, 'store']);
    Route::delete('/dashboard/media/{id}', [MediaController::class, 'destroy']);

    Route::get('/dashboard/events', [EventController::class, 'index']);
    Route::post('/dashboard/events', [EventController::class, 'store']);
    Route::delete('/dashboard/events/{id}', [EventController::class, 'destroy']);
});
