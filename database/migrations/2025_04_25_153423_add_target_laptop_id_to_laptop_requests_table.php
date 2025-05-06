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
        Schema::table('laptop_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('target_laptop_id')->nullable()->after('assigned_laptop_id');

            // Optional: set null if the laptop is deleted
            $table->foreign('target_laptop_id')
                  ->references('id')->on('laptops')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laptop_requests', function (Blueprint $table) {
            $table->dropForeign(['target_laptop_id']);
            $table->dropColumn('target_laptop_id');
        });
    }
};
