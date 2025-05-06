<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccessoriesAssignmentsTable extends Migration
{
    public function up()
    {
        Schema::create('accessories_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laptop_request_id')->constrained()->onDelete('cascade');
            $table->string('accessory_name');
            $table->integer('quantity')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('accessories_assignments');
    }
}
