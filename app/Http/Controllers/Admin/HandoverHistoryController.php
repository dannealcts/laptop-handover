<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HandoverHistory;
use App\Models\LaptopRequest;
use App\Models\ReturnRequest;

class HandoverHistoryController extends Controller
{
    /**
     * Show unified history: handovers, returns, and completed requests.
     */
    public function index()
    {
        $handoverHistories = HandoverHistory::with(['request.user', 'laptop', 'request.accessories'])->latest()->paginate(10);

        $returnHistories = ReturnRequest::with(['request.user', 'request.laptop'])
            ->where('status', 'received')
            ->latest()
            ->get();

        $completedRequests = LaptopRequest::with('user')
            ->where('status', 'completed')
            ->latest()
            ->get();

        return view('admin.laptops.history', compact('handoverHistories', 'returnHistories', 'completedRequests'));
    }

    /**
     * Store handover record after admin assignment action.
     */
    public function storeFromAssignment($requestId)
    {
        $request = LaptopRequest::with('laptop')->findOrFail($requestId);

        // Avoid duplicate entries
        if ($request->handover) {
            return;
        }

        HandoverHistory::create([
            'laptop_request_id' => $request->id,
            'laptop_id' => $request->laptop_id,
            'handover_date' => now(),
        ]);
    }
}
