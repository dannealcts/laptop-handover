<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds the 'admin_validation_form' column to return_requests table
     */
    public function up(): void
    {
        Schema::table('return_requests', function (Blueprint $table) {
            $table->string('admin_validation_form')
                  ->nullable()
                  ->comment('Stores the path to admin validation form document');
        });
    }

    /**
     * Reverse the migrations.
     * Removes the 'admin_validation_form' column from return_requests table
     */
    public function down(): void
    {
        Schema::table('return_requests', function (Blueprint $table) {
            $table->dropColumn('admin_validation_form');
        });
    }
};