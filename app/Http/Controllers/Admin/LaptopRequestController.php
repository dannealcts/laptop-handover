<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LaptopRequest;
use App\Models\Activity;

class LaptopRequestController extends Controller
{
    /**
     * Display all pending or non-completed laptop requests.
     */
    public function index(Request $request)
    {
        $query = LaptopRequest::with('user')->whereNull('completed_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->latest()->paginate(10);

        return view('admin.laptops.view-requests', compact('requests'));
    }

    /**
     * Approve a laptop request.
     */
    public function approve(LaptopRequest $request)
    {
        $request->update(['status' => 'approved']);

        Activity::create([
            'message' => "Admin approved request #{$request->id}.",
        ]);

        return back()->with('success', 'Request approved.');
    }

    /**
     * Reject a laptop request.
     */
    public function reject(LaptopRequest $request)
    {
        $request->update(['status' => 'rejected']);

        Activity::create([
            'message' => "Admin rejected request #{$request->id}.",
        ]);

        return back()->with('success', 'Request rejected.');
    }
}
