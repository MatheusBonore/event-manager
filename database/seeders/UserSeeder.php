<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
	public function run(): void
	{
		User::factory()->create([
			'name' => 'Matheus Bonore',
			'email' => 'matheus.bonore@gmail.com',
			'email_verified_at' => now(),
			'password' => Hash::make('root@eventmanager'),
			'role' => 'admin',
		]);

		User::factory()->create([
			'name' => 'System',
			'email' => 'root@email.com',
			'email_verified_at' => now(),
			'password' => Hash::make('root@eventmanager'),
			'role' => 'admin',
		]);

		User::factory(250)->create();
	}
}