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
        Schema::table('user_ratings', function (Blueprint $table) {
            $table->foreignId('device_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_ratings', function (Blueprint $table) {
            // Drop the foreign key and the column if rolling back
            $table->dropForeign(['device_id']);
            $table->dropColumn('device_id');
        });
    }
};
