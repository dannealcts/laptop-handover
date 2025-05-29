<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LaptopRequest;
use Illuminate\Support\Facades\Mail;
use App\Mail\UpgradeEligibilityMail;
use App\Models\Laptop;
use App\Notifications\UpgradeNotification;
use Illuminate\Support\Facades\Notification;

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

    public function notifyUpgrade($id)
    {
        $laptop = Laptop::with(['requests.user'])->findOrFail($id);

        $latestRequest = $laptop->requests
            ->whereIn('status', ['approved', 'assigned', 'completed'])
            ->sortByDesc('created_at')
            ->first();

        $user = $latestRequest?->user;

        if ($user) {
            // ✅ Send using the user model as notifiable
            Mail::to($user->email)->send(new UpgradeEligibilityMail($user, $laptop));

            // ✅ Update status
            $laptop->upgrade_notification_status = 'notified';
            $laptop->save();

            return back()->with('success', 'Notification sent to ' . $user->name . '.');
        }

        return back()->with('error', 'No assigned user found for this laptop.');
    }

}
