<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Laptop;
use App\Models\LaptopRequest;
use App\Models\Activity;

class AdminController extends Controller
{
    // Admin dashboard overview
    public function dashboard()
    {
        $totalLaptops = Laptop::count();
        $pendingRequests = LaptopRequest::where('status', 'pending')->count();
        $assignedDevices = Laptop::where('status', 'assigned')->count();
        $maintenanceDevices = Laptop::where('status', 'maintenance')->count();

        $recentActivities = Activity::latest()->take(5)->get()->map(function ($item) {
            return [
                'message' => $item->message,
                'time' => $item->created_at->diffForHumans()
            ];
        });

        $allActivities = Activity::latest()->get()->map(function ($item) {
            return [
                'message' => $item->message,
                'time' => $item->created_at->diffForHumans()
            ];
        });

        $eligibleLaptops = Laptop::whereNotNull('purchase_date')
            ->where('status', 'assigned')
            ->with(['requests.user'])
            ->get()
            ->filter(function ($laptop) {
                return $laptop->isEligibleForUpgrade();
            });

        return view('admin.dashboard', compact(
            'totalLaptops',
            'pendingRequests',
            'assignedDevices',
            'maintenanceDevices',
            'recentActivities',
            'allActivities',
            'eligibleLaptops'
        ));
    }

    // View and filter activity logs
    public function viewActivities(Request $request)
    {
        $query = Activity::query();

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('created_at', [
                $request->from_date . ' 00:00:00',
                $request->to_date . ' 23:59:59',
            ]);
        }

        if ($request->filled('keyword')) {
            $query->where('message', 'like', '%' . $request->keyword . '%'); // ✅ Fixed
        }

        $activities = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.activities', compact('activities'));
    }

}
