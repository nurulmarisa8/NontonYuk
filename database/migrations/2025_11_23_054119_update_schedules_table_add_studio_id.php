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
        Schema::table('schedules', function (Blueprint $table) {
            $table->unsignedBigInteger('studio_id')->nullable()->after('movie_id');
            $table->foreign('studio_id')->references('id')->on('studios')->onDelete('set null');

            // Drop the old room column since we're now using studio_id
            $table->dropColumn('room');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->string('room')->after('showtime');
            $table->dropForeign(['studio_id']);
            $table->dropColumn('studio_id');
        });
    }
};
