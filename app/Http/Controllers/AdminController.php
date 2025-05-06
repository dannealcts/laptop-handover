<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Laptop;
use App\Models\LaptopRequest;
use App\Models\Activity;
use App\Models\HandoverHistory;
use App\Models\ReturnRequest;


class AdminController extends Controller
{
    //Dashboard
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

        return view('admin.dashboard', compact(
            'totalLaptops',
            'pendingRequests',
            'assignedDevices',
            'maintenanceDevices',
            'recentActivities',
            'allActivities'
        ));
    }

    //History Page
    public function history()
    {
        $handoverHistories = HandoverHistory::with(['request.user', 'laptop'])
            ->latest()
            ->paginate(10, ['*'], 'handover_page');

        $returnHistories = ReturnRequest::with(['user', 'laptop']) // Fixed relationship
            ->latest()
            ->paginate(10, ['*'], 'return_page');

        $completedRequests = LaptopRequest::with(['user', 'laptop'])
            ->whereNotNull('completed_at')
            ->latest()
            ->paginate(10, ['*'], 'completed_page');

        return view('admin.laptops.history', compact(
            'handoverHistories',
            'returnHistories',
            'completedRequests'
        ));
    }

    // Recent Activities
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
            $query->where('description', 'like', '%' . $request->keyword . '%');
        }

        $activities = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.laptops.activities', compact('activities'));
        
    }

}
