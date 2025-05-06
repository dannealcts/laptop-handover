<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
            public function up()
    {
        Schema::table('laptop_requests', function (Blueprint $table) {
            $table->string('assigned_part')->nullable();
            $table->string('assigned_upgrade')->nullable();
        });
    }

    public function down()
    {
        Schema::table('laptop_requests', function (Blueprint $table) {
            $table->dropColumn(['assigned_part', 'assigned_upgrade']);
        });
    }

};
