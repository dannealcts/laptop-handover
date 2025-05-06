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
        Schema::table('laptops', function (Blueprint $table) {
            $table->string('status')->default('available')->change(); // or enum if you used enum
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
    }
};
