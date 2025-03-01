<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('events', function (Blueprint $table) {
			$table->uuid('event')->primary();
			$table->foreignUuid('users_user')->constrained('users', 'user')->cascadeOnDelete();
			$table->string('title');
			$table->text('description');
			$table->dateTime('start_time');
			$table->dateTime('end_time');
			$table->string('location');
			$table->integer('capacity');
			$table->enum('status', ['open', 'closed', 'canceled'])->default('open');
			$table->timestamp('created_at')->useCurrent();
			$table->timestamp('updated_at')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('events');
	}
};
