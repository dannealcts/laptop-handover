<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Laptop;
use App\Models\ReturnRequest;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;

class ReturnRequestController extends Controller
{
    // ================= STAFF METHODS =================

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

        // Check if a pending return request already exists
        $existingRequest = ReturnRequest::where('user_id', Auth::id())
            ->where('laptop_id', $request->laptop_id)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return redirect()->back()->with('error', 'You have already submitted a return request for this laptop.');
        }

        // Proceed to create new return request
        ReturnRequest::create([
            'user_id' => Auth::id(),
            'laptop_id' => $request->laptop_id,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return redirect()->route('staff.dashboard')->with('success', 'Laptop return request submitted successfully.');
    }

    // ================= ADMIN METHODS =================

    /**
     * View list of pending return requests.
     */
    public function adminIndex()
    {
        $returns = ReturnRequest::with(['user', 'laptop'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('admin.laptops.view-return-requests', [
            'returnRequests' => $returns,
        ]);
    }

    /**
     * Complete return request by uploading signed validation form.
     */
    public function complete(Request $request, $id)
    {
        $request->validate([
            'admin_validation_form' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $returnRequest = ReturnRequest::with('laptop')->findOrFail($id);

        if ($request->hasFile('admin_validation_form')) {
            $file = $request->file('admin_validation_form');
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('return-forms', $filename, 'public');

            $returnRequest->admin_validation_form = $filePath;
            $returnRequest->status = 'received';
            $returnRequest->received_at = now();
            $returnRequest->save();

            $returnRequest->laptop->status = 'maintenance';
            $returnRequest->laptop->save();

            Activity::create([
                'message' => "Laptop returned and validated: {$returnRequest->laptop->asset_tag}",
                'user_id' => Auth::id(),
            ]);

            return back()->with('success', 'Return request marked as received.');
        }

        return back()->with('error', 'File not found in request.');
    }

    /**
     * Delete a return request (used to remove accidental duplicates)
     */
    public function delete($id)
    {
        $request = ReturnRequest::findOrFail($id);
        $request->delete();

        return back()->with('success', 'Return request deleted successfully.');
    }
}
