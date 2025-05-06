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
        Schema::create('handover_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laptop_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('laptop_id')->constrained()->onDelete('cascade');
            $table->timestamp('handover_date')->nullable(); // or $table->dateTime('handover_date')
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('handover_histories');
    }
};
