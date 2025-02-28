<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('users_has_events', function (Blueprint $table) {
			$table->foreignUuid('users_user')->constrained('users', 'user')->cascadeOnDelete();
			$table->foreignUuid('events_event')->constrained('events', 'event')->cascadeOnDelete();

			$table->primary(['users_user', 'events_event']);

			$table->timestamp('created_at')->useCurrent();
			$table->timestamp('updated_at')->nullable();

			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('users_has_events');
	}
};
