<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Laptop;
use App\Models\ReturnRequest;
use Illuminate\Support\Facades\Auth;

class ReturnRequestController extends Controller
{
    /**
     * Show return form with list of assigned laptops.
     */
    public function create()
    {
        $assignedLaptops = Laptop::where('status', 'assigned')
            ->whereHas('requests', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->get();

        return view('staff.return-laptop', compact('assignedLaptops'));
    }

    /**
     * Store return request from staff.
     */
    public function store(Request $request)
    {
        $request->validate([
            'laptop_id' => 'required|exists:laptops,id',
            'reason' => 'required|string',
        ]);

        $existingRequest = ReturnRequest::where('user_id', Auth::id())
            ->where('laptop_id', $request->laptop_id)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return redirect()->back()->with('error', 'You have already submitted a return request for this laptop.');
        }

        ReturnRequest::create([
            'user_id' => Auth::id(),
            'laptop_id' => $request->laptop_id,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return redirect()->route('staff.dashboard')->with('success', 'Laptop return request submitted successfully.');
    }
}
