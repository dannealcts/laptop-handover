<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LaptopRequest;
use Illuminate\Support\Facades\Mail;
use App\Mail\UpgradeEligibilityMail;

class NotificationController extends Controller
{
    // Send upgrade eligibility email to user
    public function sendUpgradeEmail($userId)
    {
        $user = User::findOrFail($userId);

        $latestRequest = LaptopRequest::where('user_id', $user->id)
            ->whereNotNull('assigned_laptop_id')
            ->whereIn('status', ['approved', 'assigned', 'completed'])
            ->with('assignedLaptop')
            ->orderByDesc('created_at')
            ->first();

        if ($latestRequest && $latestRequest->assignedLaptop) {
            Mail::to($user->email)->send(new UpgradeEligibilityMail($user, $latestRequest->assignedLaptop));
            return back()->with('success', 'Notification sent to ' . $user->name);
        }

        return back()->with('error', 'No assigned laptop found for this user.');
    }
}
