<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Activity;
use Carbon\Carbon;

class ClearOldActivities extends Command
{
    protected $signature = 'activities:clear-old';
    protected $description = 'Delete activity logs older than 1 day';

    public function handle()
    {
        $cutoff = Carbon::now()->subDay();
        Activity::where('created_at', '<', $cutoff)->delete();
        $this->info('Old activity logs deleted successfully.');
    }
}
