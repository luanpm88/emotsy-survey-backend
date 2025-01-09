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
        Schema::table('surveys', function (Blueprint $table) {
            // Add user_id column
            $table->unsignedBigInteger('user_id')->nullable();

            // Add foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('devices', function (Blueprint $table) {
            // Add user_id column
            $table->unsignedBigInteger('user_id')->nullable();

            // Add foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surveys', function (Blueprint $table) {
            // Drop the foreign key and the column if rolling back
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('devices', function (Blueprint $table) {
            // Drop the foreign key and the column if rolling back
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
