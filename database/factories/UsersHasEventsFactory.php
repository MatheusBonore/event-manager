<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UsersHasEvents>
 */
class UsersHasEventsFactory extends Factory
{
	/**
	 * Define the model's default state.
	 *
	 * @return array<string, mixed>
	 */
	public function definition(): array
	{
		return [
			'users_user' => $this->faker->randomElement(User::all()->pluck('user')->toArray() ?? User::factory()->create()->user),
			'events_event' => $this->faker->randomElement(Event::all()->pluck('event')->toArray() ?? Event::factory()->create()->event),
			'confirmed' => true,
		];
	}
}
