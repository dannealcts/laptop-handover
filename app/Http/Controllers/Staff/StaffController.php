<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\LaptopRequest;
use Carbon\Carbon;

class StaffController extends Controller
{
    /**
     * Display the staff dashboard showing assigned laptop and upgrade eligibility.
     */
    public function dashboard()
    {
        $user = Auth::user();

        $latestAssignedRequest = LaptopRequest::where('user_id', $user->id)
            ->whereNotNull('assigned_laptop_id')
            ->whereIn('status', ['approved', 'assigned', 'handovered', 'completed'])
            ->with('assignedLaptop')
            ->latest()
            ->first();

        $assignedLaptop = $latestAssignedRequest?->assignedLaptop;
        $eligibleLaptop = null;
        $timeLeftReadable = null;

        if ($assignedLaptop && $assignedLaptop->purchase_date) {
            $purchaseDate = Carbon::parse($assignedLaptop->purchase_date);
            $eligibleDate = $purchaseDate->copy()->addYears(5);

            if ($eligibleDate->isPast()) {
                $eligibleLaptop = $assignedLaptop;
            } else {
                $diff = Carbon::now()->diff($eligibleDate);
                $timeLeftReadable = "{$diff->y} year(s), {$diff->m} month(s), {$diff->d} day(s)";
            }
        }

        return view('staff.dashboard', compact(
            'assignedLaptop',
            'eligibleLaptop',
            'timeLeftReadable'
        ));
    }
}
