<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Laptop;
use App\Models\LaptopRequest;
use App\Models\AccessoryAssignment;
use App\Models\Activity;

class LaptopAssignmentController extends Controller
{
    /**
     * Show form to assign a laptop and accessories.
     */
    public function assignForm($id)
    {
        $request = LaptopRequest::with('user')->findOrFail($id);

        if (($request->type === 'replacement' && $request->replacement_part !== 'Laptop') || $request->type === 'upgrade') {
            return back()->with('error', 'This request does not require a new laptop assignment.');
        }

        $availableLaptops = Laptop::where('status', 'available')->get();

        return view('admin.laptops.assign-laptop', compact('request', 'availableLaptops'));
    }

    /**
     * Store assigned laptop and accessories.
     */
    public function assignLaptop(Request $request, $id)
    {
        $validated = $request->validate([
            'laptop_id' => 'required|exists:laptops,id',
            'accessories.*' => 'nullable|string',
            'accessories_quantity.*' => 'nullable|integer|min:1',
        ]);

        $laptopRequest = LaptopRequest::findOrFail($id);

        $laptopRequest->update([
            'laptop_id' => $validated['laptop_id'],
            'assigned_laptop_id' => $validated['laptop_id'],
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        Laptop::where('id', $validated['laptop_id'])->update(['status' => 'assigned']);

        if ($request->has('accessories')) {
            foreach ($request->accessories as $index => $accessory) {
                if ($accessory) {
                    AccessoryAssignment::create([
                        'laptop_request_id' => $laptopRequest->id,
                        'accessory_name' => $accessory,
                        'quantity' => $request->accessories_quantity[$index] ?? 1,
                    ]);
                }
            }
        }

        Activity::create([
            'message' => "Laptop assigned with accessories to request #{$laptopRequest->id}.",
        ]);

        app(HandoverHistoryController::class)->storeFromAssignment($laptopRequest->id);

        return redirect()->route('admin.laptops.index')->with('success', 'Laptop and accessories assigned successfully!');
    }

    /**
     * Show form to assign non-laptop part or upgrade.
     */
    public function assignPartUpgradeForm($id)
    {
        $request = LaptopRequest::with(['user', 'assignedLaptop'])->findOrFail($id);

        if (($request->type === 'replacement' && $request->replacement_part !== 'Laptop') || $request->type === 'upgrade') {
            return view('admin.laptops.assign-part-upgrade', compact('request'));
        }

        return redirect()->route('admin.laptops.index')->with('error', 'This request does not require a part/upgrade assignment.');
    }

    /**
     * Store assigned part or upgrade.
     */
    public function storeAssignedPartUpgrade(Request $request, $id)
    {
        $request->validate([
            'assigned_part' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
        ]);

        $laptopRequest = LaptopRequest::with(['assignedLaptop', 'user'])->findOrFail($id);

        $laptopRequest->update([
            'assigned_part' => $request->assigned_part,
            'assigned_quantity' => $request->quantity,
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $laptopInfo = $laptopRequest->assignedLaptop
            ? " (Laptop SN: {$laptopRequest->assignedLaptop->serial_number})"
            : "";

        Activity::create([
            'message' => "Assigned part/upgrade '{$request->assigned_part}' (Qty: {$request->quantity}) to {$laptopRequest->user->name}{$laptopInfo} for request #{$laptopRequest->id}.",
        ]);

        return redirect()->route('admin.laptops.index')->with('success', 'Part/Upgrade assigned and marked as completed.');
    }
}
