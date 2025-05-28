<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Laptop;
use Illuminate\Support\Facades\Mail;
use App\Mail\EligibleForUpgradeMail;
use Carbon\Carbon;

class NotifyEligibleUpgrades extends Command
{
    protected $signature = 'notify:eligible-upgrades';
    protected $description = 'Send email notifications to users with laptops eligible for upgrade';

    public function handle()
    {
        $today = Carbon::now();

        // Filter only laptops that are 5 years or more old
        $laptops = Laptop::whereNotNull('purchase_date')
            ->whereDate('purchase_date', '<=', $today->copy()->subYears(5))
            ->with('assignedTo') // Make sure this relation exists
            ->get();

        foreach ($laptops as $laptop) {
            if ($laptop->assignedTo && $laptop->assignedTo->email) {
                Mail::to($laptop->assignedTo->email)->send(new EligibleForUpgradeMail($laptop->assignedTo, $laptop));
                $this->info("Email sent to: " . $laptop->assignedTo->email);
            }
        }
    }
}
