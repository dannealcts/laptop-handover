<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\HandoverHistory;

class HandoverHistoryController extends Controller
{
    /**
     * Display staff's own handover history.
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
                'accessories' => $history->request->accessories->map(function ($accessory) {
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
