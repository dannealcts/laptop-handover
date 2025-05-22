<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\HandoverHistory;
use App\Models\LaptopRequest;
use App\Models\ReturnRequest;

class HandoverHistoryController extends Controller
{
    /**
     * ADMIN SIDE:
     * Show unified history view including:
     * - handovers
     * - return requests
     * - completed requests (part/upgrade/laptop)
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
     * Store handover record after assignment.
     */
    public function storeFromAssignment($requestId)
    {
        $request = LaptopRequest::with('laptop')->findOrFail($requestId);

        // Avoid duplicates
        if ($request->handover) {
            return;
        }

        HandoverHistory::create([
            'laptop_request_id' => $request->id,
            'laptop_id' => $request->laptop_id,
            'handover_date' => now(),
        ]);
    }

    /**
     * STAFF SIDE:
     * View their own handover history.
     */
    public function myHistory()
    {
        $userId = Auth::id();

        $handoverHistories = HandoverHistory::whereHas('request', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->with(['laptop', 'request.accessories'])
        ->latest()
        ->paginate(10);

        $histories = $handoverHistories->map(function ($history) {
            return [
                'type' => $history->request->type,
                'requested_part' => $history->request->replacement_part ?? '-',
                'assigned_part' => $history->request->assigned_part ?? '-',
                'justification' => $history->request->justification ?? $history->request->other_justification,
                'status' => ucfirst($history->request->status),
                'laptop' => $history->laptop ? $history->laptop->asset_tag . ' (' . $history->laptop->model . ')' : '-',
                'date' => $history->created_at,
                'accessories' => $history->request->accessories->map(function ($accessory) { // ✅ FIXED
                    return [
                        'accessory_name' => $accessory->accessory_name,
                        'quantity' => $accessory->quantity,
                    ];
                })->toArray(),
            ];
        });

        return view('staff.request-history', ['histories' => $histories]);
    }
}
