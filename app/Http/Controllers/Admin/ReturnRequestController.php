<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReturnRequest;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;

class ReturnRequestController extends Controller
{
    /**
     * View list of pending return requests.
     */
    public function index()
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

            $returnRequest->update([
                'admin_validation_form' => $filePath,
                'status' => 'received',
                'received_at' => now(),
            ]);

            $returnRequest->laptop->update(['status' => 'maintenance']);

            Activity::create([
                'message' => "Laptop returned and validated: {$returnRequest->laptop->asset_tag}",
                'user_id' => Auth::id(),
            ]);

            return back()->with('success', 'Return request marked as received.');
        }

        return back()->with('error', 'File not found in request.');
    }

    /**
     * Delete a return request (used to remove accidental duplicates).
     */
    public function delete($id)
    {
        $request = ReturnRequest::findOrFail($id);
        $request->delete();

        return back()->with('success', 'Return request deleted successfully.');
    }
}
