<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
	public function run(): void
	{
		Event::factory(10)->create();
	}
}