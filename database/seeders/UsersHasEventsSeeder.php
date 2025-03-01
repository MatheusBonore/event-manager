<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use App\Models\UsersHasEvents;
use Illuminate\Database\Seeder;

class UsersHasEventsSeeder extends Seeder
{
	public function run(): void
	{
		$emails = User::where('role', 'admin')->pluck('email')->toArray();

		$users = User::all();
		$events = Event::all();

		// Removendo $emails para, em seguida, priorizar apenas os $emails, garantindo que nunca faltem eventos para eles.

		$filteredUsers = $users->whereNotIn('email', $emails);

		if ($filteredUsers->isNotEmpty() && $events->isNotEmpty()) {
			$events->each(function ($event) use ($filteredUsers) {
				$randomUsers = $filteredUsers->random(rand(0, min($event->capacity, $filteredUsers->count())));

				foreach ($randomUsers as $user) {
					UsersHasEvents::factory()->create([
						'users_user' => $user->user,
						'events_event' => $event->event,
					]);
				}
			});
		}

		// Priorizando eventos para os $emails como administradores em eventos aleatórios.

		$filteredUsers = $users->whereIn('email', $emails);

		if ($filteredUsers->isNotEmpty() && $events->isNotEmpty()) {
			$events->each(function ($event) use ($filteredUsers) {
				UsersHasEvents::factory()->create([
					'users_user' => $filteredUsers->random()->user,
					'events_event' => $event->event,
				]);
			});
		}
	}
}