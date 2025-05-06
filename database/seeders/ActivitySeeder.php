<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Activity;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        Activity::create([
            'message' => 'System initialized',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

