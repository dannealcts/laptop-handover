<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Laptop;
use App\Models\LaptopRequest;
use App\Models\ReturnRequest;
use App\Models\Activity;

class LaptopRequestController extends Controller
{
    /**
     * Show staff request and return history.
     */
    public function index()
    {
        $user = Auth::user();

        $requests = LaptopRequest::where('user_id', $user->id)
            ->with(['laptop', 'targetLaptop'])
            ->get()
            ->map(function ($item) {
                $associatedLaptop = $item->targetLaptop ?? $item->laptop;

                return [
                    'type' => ucfirst($item->type),
                    'requested_part' => $item->replacement_part ?? $item->upgrade_type ?? '-',
                    'assigned_part' => $item->assigned_part ?? '-',
                    'justification' => $item->justification,
                    'status' => ucfirst($item->status),
                    'laptop' => $associatedLaptop
                        ? $associatedLaptop->asset_tag . ' (' . $associatedLaptop->model . ')' : '-',
                    'date' => $item->created_at,
                    'category' => 'Request',
                ];
            });

        $returns = ReturnRequest::with('laptop')
            ->where('user_id', $user->id)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'Return',
                    'requested_part' => '-',
                    'assigned_part' => '-',
                    'justification' => $item->reason,
                    'status' => ucfirst($item->status),
                    'laptop' => optional($item->laptop)?->asset_tag . ' (' . optional($item->laptop)?->model . ')',
                    'date' => $item->received_at ?? $item->created_at,
                    'category' => 'Return',
                ];
            });

        $histories = $requests->merge($returns)->sortByDesc('date');

        return view('staff.request-history', compact('histories'));
    }

    /**
     * Show form for new/replacement/upgrade laptop request.
     */
    public function create()
    {
        $user = Auth::user();

        $assignedLaptops = Laptop::where('status', 'assigned')
            ->whereIn('id', function ($query) use ($user) {
                $query->select('assigned_laptop_id')
                    ->from('laptop_requests')
                    ->where('user_id', $user->id)
                    ->whereNotNull('assigned_laptop_id')
                    ->where('status', 'completed');
            })->get();

        return view('staff.make-request', compact('assignedLaptops'));
    }

    /**
     * Submit a new laptop request.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:new,replacement,upgrade',
            'justification' => 'required',
            'replacement_part' => 'required_if:type,replacement|nullable|in:battery,keyboard,motherboard,screen,charger,laptop,others',
            'upgrade_type' => 'required_if:type,upgrade|nullable|in:memory,processor,hard_disk',
            'target_laptop_id' => 'required_if:type,replacement,upgrade|nullable|exists:laptops,id',
            'other_replacement' => 'nullable|string',
            'other_justification' => 'nullable|string',
            'signed_form' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $signedFormPath = $request->file('signed_form')->store('signed_forms', 'public');

        $assignedLaptop = LaptopRequest::where('user_id', Auth::id())
            ->where('status', 'completed')
            ->whereNotNull('assigned_laptop_id')
            ->latest('completed_at')
            ->with('assignedLaptop')
            ->first()?->assignedLaptop;

        $laptopRequest = LaptopRequest::create([
            'user_id' => Auth::id(),
            'type' => $validated['type'],
            'replacement_part' => $validated['replacement_part'],
            'upgrade_type' => $validated['upgrade_type'],
            'justification' => $validated['justification'] === 'others'
                ? $validated['other_justification']
                : $validated['justification'],
            'other_replacement' => $validated['other_replacement'],
            'other_justification' => $validated['other_justification'],
            'signed_form' => $signedFormPath,
            'assigned_laptop_id' => $assignedLaptop?->id,
            'target_laptop_id' => $validated['target_laptop_id'] ?? null,
        ]);

        Activity::create([
            'message' => 'New laptop request submitted by ' . Auth::user()->name . ' (Request ID: ' . $laptopRequest->id . ')',
        ]);

        return redirect()->route('staff.dashboard')->with('success', 'Laptop request submitted successfully!');
    }
}
