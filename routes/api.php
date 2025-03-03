<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EventController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
	Route::post('/logout', [AuthController::class, 'logout']);
	Route::get('/user', function (Request $request) {
		return $request->user();
	});

	Route::get('events', [EventController::class, 'index']);
	Route::post('events', [EventController::class, 'store']);
	Route::get('events/{id}', [EventController::class, 'show']);
	Route::put('events/{id}', [EventController::class, 'update']);
	Route::delete('events/{id}', [EventController::class, 'destroy']);
});