<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('laptop_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('assigned_laptop_id')->nullable()->after('status');
            $table->foreign('assigned_laptop_id')->references('id')->on('laptops')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('laptop_requests', function (Blueprint $table) {
            $table->dropForeign(['assigned_laptop_id']);
            $table->dropColumn('assigned_laptop_id');
        });
    }

};
