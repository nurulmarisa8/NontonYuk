<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('status', 50)->default('confirmed')->change(); // Update status options
            $table->dateTime('locked_until')->nullable(); // For seat locking functionality
            $table->string('ticket_id')->unique()->nullable(); // Unique ticket ID
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('locked_until');
            $table->dropColumn('ticket_id');
        });
    }
};
