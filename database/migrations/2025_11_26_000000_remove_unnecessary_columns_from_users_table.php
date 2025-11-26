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
        Schema::table('users', function (Blueprint $table) {
            // Drop the email_verified_at column
            $table->dropColumn('email_verified_at');
            
            // Drop the whatsapp_number column
            $table->dropColumn('whatsapp_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add back the email_verified_at column
            $table->timestamp('email_verified_at')->nullable();
            
            // Add back the whatsapp_number column
            $table->string('whatsapp_number')->nullable();
        });
    }
};