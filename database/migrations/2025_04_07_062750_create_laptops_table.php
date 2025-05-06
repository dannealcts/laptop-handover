<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laptops', function (Blueprint $table) {
            $table->id();
            $table->string('asset_tag')->unique();
            $table->string('brand');
            $table->string('model');
            $table->string('serial_number')->unique();
            $table->text('specs')->nullable();
            $table->string('status')->default('available'); // available, assigned, under_maintenance, etc.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laptops');
    }
};
