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
        Schema::table('movies', function (Blueprint $table) {
            $table->string('status')->default('active')->after('release_date'); // active, inactive, coming_soon, now_showing
            $table->string('age_rating')->nullable()->after('genre'); // e.g., PG, PG-13, R, etc.
            $table->string('poster_url')->nullable()->after('title'); // URL for movie poster
        });
    }

    public function down(): void
    {
        Schema::table('movies', function (Blueprint $table) {
        });
    }
};
