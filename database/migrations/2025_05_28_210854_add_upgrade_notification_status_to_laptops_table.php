<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('laptops', function (Blueprint $table) {
            $table->string('upgrade_notification_status')->default('not_notified')->after('status');
        });
    }

    public function down()
    {
        Schema::table('laptops', function (Blueprint $table) {
            $table->dropColumn('upgrade_notification_status');
        });
    }
};
