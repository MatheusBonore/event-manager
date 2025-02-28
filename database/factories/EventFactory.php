<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
	/**
	 * Define the model's default state.
	 *
	 * @return array<string, mixed>
	 */
	public function definition(): array
	{
		$startTime = $this->faker->dateTimeBetween('+1 days', '+1 month');
		$endTime = (clone $startTime)->modify('+' . rand(1, 5) . ' hours');

		return [
			'event' => Str::uuid(),
			'title' => $this->faker->sentence(3),
			'description' => $this->faker->paragraph(),
			'start_time' => $startTime,
			'end_time' => $endTime,
			'location' => $this->faker->address(),
			'capacity' => $this->faker->numberBetween(10, 500),
			'status' => $this->faker->randomElement(['open', 'closed', 'canceled'])
		];
	}
}
