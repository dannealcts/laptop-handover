<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HandoverHistory;
use App\Models\ReturnRequest;
use App\Models\LaptopRequest;

class HistoryController extends Controller
{
    // Unified view of handovers, returns, and completed requests
    public function index()
    {
        $handoverHistories = HandoverHistory::with(['request.user', 'laptop'])
            ->latest()
            ->paginate(10, ['*'], 'handover_page');

        $returnHistories = ReturnRequest::with(['user', 'laptop'])
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
}
