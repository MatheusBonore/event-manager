<?php

use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\EventController;
use App\Http\Controllers\Web\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
	return view('welcome');
});

Route::middleware('auth')->group(function () {
	Route::get('/dashboard', [DashboardController::class, 'index'])
		->name('dashboard');

	Route::get('/profile', [ProfileController::class, 'edit'])
		->name('profile.edit');

	Route::patch('/profile', [ProfileController::class, 'update'])
		->name('profile.update');

	Route::delete('/profile', [ProfileController::class, 'destroy'])
		->name('profile.destroy');

	Route::get('/events', [EventController::class, 'index'])
		->name('events');

	Route::middleware('admin')->group(function() {
		Route::post('/events', [EventController::class, 'store'])
			->name('events.store');

		Route::put('events/{event}', [EventController::class, 'update'])
			->name('events.update');
	});

	Route::post('/events/{event}/participate', [EventController::class, 'participate'])
		->name('events.participate');

	Route::post('/events/{event}/leave', [EventController::class, 'leave'])
		->name('events.leave');
});

Route::get('/events/{event}/confirm/{action}/{token}', [EventController::class, 'confirmAction']);

require __DIR__ . '/auth.php';
