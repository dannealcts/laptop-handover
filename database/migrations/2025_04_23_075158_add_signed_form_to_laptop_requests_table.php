<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laptop_requests', function (Blueprint $table) {
            $table->string('signed_form')->nullable()->after('other_justification');
        });
    }

    public function down(): void
    {
        Schema::table('laptop_requests', function (Blueprint $table) {
            $table->dropColumn('signed_form');
        });
    }
};
