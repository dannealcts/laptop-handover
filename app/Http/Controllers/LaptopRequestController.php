<?php

namespace App\Http\Controllers;

use App\Models\Laptop;
use App\Models\LaptopRequest;
use App\Models\Activity;
use App\Models\ReturnRequest;
use App\Models\AccessoryAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\User;
use Carbon\Carbon;

class LaptopRequestController extends Controller
{
    /*
    |==========================================================================
    | STAFF METHODS
    |==========================================================================
    */

    /** View Staff Request and Return History */
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

        return view('staff.my-requests', compact('histories'));
    }

    /** Show Staff Laptop Request Form */
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

    /** Store Staff Laptop Request */
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

        $path = $request->file('signed_form')->store('signed_forms', 'public');

        $assignedLaptop = LaptopRequest::where('user_id', Auth::id())
            ->where('status', 'completed')
            ->whereNotNull('assigned_laptop_id')
            ->latest('completed_at')
            ->with('assignedLaptop')
            ->first()?->assignedLaptop;

        $created = LaptopRequest::create([
            'user_id' => Auth::id(),
            'type' => $validated['type'],
            'replacement_part' => $validated['replacement_part'],
            'upgrade_type' => $validated['upgrade_type'],
            'justification' => $validated['justification'] === 'others'
                ? $validated['other_justification']
                : $validated['justification'],
            'other_replacement' => $validated['other_replacement'],
            'other_justification' => $validated['other_justification'],
            'signed_form' => $path,
            'assigned_laptop_id' => $assignedLaptop?->id,
            'target_laptop_id' => $validated['target_laptop_id'] ?? null,
        ]);

        Activity::create([
            'message' => 'New laptop request submitted by ' . Auth::user()->name . ' (Request ID: ' . $created->id . ')',
        ]);

        return redirect()->route('staff.dashboard')->with('success', 'Laptop request submitted successfully!');
    }

    /*
    |==========================================================================
    | ADMIN METHODS
    |==========================================================================
    */

    /** View All Staff Requests */
    public function adminIndex(Request $request)
    {
        $query = LaptopRequest::with('user')->whereNull('completed_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->latest()->paginate(10);

        return view('admin.laptops.view-requests', compact('requests'));
    }

    /** Approve Staff Request */
    public function approve(LaptopRequest $request)
    {
        $request->update(['status' => 'approved']);

        Activity::create([
            'message' => "Admin approved request #{$request->id}.",
        ]);

        return back()->with('success', 'Request approved.');
    }

    /** Reject Staff Request */
    public function reject(LaptopRequest $request)
    {
        $request->update(['status' => 'rejected']);

        Activity::create([
            'message' => "Admin rejected request #{$request->id}.",
        ]);

        return back()->with('success', 'Request rejected.');
    }

    /** Assign Laptop and Accessories */
    public function assignForm($id)
    {
        $request = LaptopRequest::with('user')->findOrFail($id);

        if (($request->type === 'replacement' && $request->replacement_part !== 'Laptop') || $request->type === 'upgrade') {
            return back()->with('error', 'This request does not require a new laptop assignment.');
        }

        $availableLaptops = Laptop::where('status', 'available')->get();

        return view('admin.laptops.assign-laptop', compact('request', 'availableLaptops'));
    }

    /** Store Laptop Assignment */
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

        return redirect()->route('admin.view-requests')->with('success', 'Laptop and accessories assigned successfully!');
    }

    /** Show Export Form */
    public function exportForm(Request $request)
    {
        $staffList = User::where('role', 'staff')->get();
        $requests = [];

        if ($request->filled(['start_date', 'end_date'])) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->endOfDay();
        
            $query = LaptopRequest::with(['user', 'laptop', 'accessories'])
                ->whereBetween('created_at', [$start, $end])
                ->whereIn('status', ['approved', 'completed']);
        
            if ($request->filled('staff_ids')) {
                $query->whereIn('user_id', $request->staff_ids);
            }
        
            $requests = $query->get();
        }        

        return view('admin.laptops.export-request', [
            'staffList' => $staffList,
            'requests' => $requests,
        ]);
    }

    /** Search Staff for Export */
    public function searchStaff(Request $request)
    {
        $keyword = $request->input('keyword');

        $staff = \App\Models\User::where(function ($q) use ($keyword) {
            $q->where('name', 'LIKE', "%{$keyword}%")->orWhere('email', 'LIKE', "%{$keyword}%");
        })->where('role', 'staff')->first();

        if (!$staff) {
            return back()->with('error', 'Staff not found.');
        }

        $requests = LaptopRequest::with('laptop')->where('user_id', $staff->id)->whereIn('status', ['approved', 'completed'])->get();

        return view('admin.laptops.export-request', compact('staff', 'requests'));
    }

    // Select Staff To Export
    public function exportSelected(Request $request)
    {
        $request->validate([
            'selected_requests' => 'required|array',
            'selected_requests.*' => 'exists:laptop_requests,id',
        ]);

        $requests = LaptopRequest::with(['user', 'laptop', 'accessories'])
            ->whereIn('id', $request->selected_requests)
            ->get();

        if ($requests->isEmpty()) {
            return back()->with('error', 'No selected requests found.');
        }

        $spreadsheet = IOFactory::load(storage_path('app/templates/PURCHASE REQUISITION FORM.xlsx'));
        $sheet = $spreadsheet->getActiveSheet();

        $grouped = $requests->groupBy(function ($req) {
            return implode('|', [
                $req->type,
                $req->laptop?->brand,
                $req->laptop?->model,
                $req->assigned_part,
                $req->upgrade_type,
                $req->replacement_part,
            ]);
        });

        $row = 18;
        $itemIndex = 1;

        foreach ($grouped as $group) {
            $first = $group->first();
            $staffNames = $group->pluck('user.name')->join(', ');

            // Collect unique accessories
            $accessoryText = collect();
            foreach ($group as $req) {
                if ($req->accessories && $req->accessories->isNotEmpty()) {
                    $accessoryText = $accessoryText->merge($req->accessories->map(function ($a) {
                        return $a->accessory_name . ' (x' . $a->quantity . ')';
                    }));
                }
            }
            $accessoryStr = $accessoryText->unique()->implode(', ');

            $item = $this->getExportItemDescription($first);
            $mainLine = trim($item . ($accessoryStr ? ", $accessoryStr" : ''));
            $descriptionWithNames = "$mainLine – $staffNames";

            // Row 1: Item line with requester names
            $sheet->setCellValue("B{$row}", $itemIndex);
            $sheet->setCellValue("C{$row}", $descriptionWithNames);
            $sheet->setCellValue("J{$row}", $group->count());
            $sheet->setCellValue("K{$row}", 'Unit');

            // Row 2: Specification
            $row++;
            $sheet->setCellValue("C{$row}", $this->getLaptopSpecsText($first));

            $row++;
            $itemIndex++;
        }

        // Optional: Admin remark
        $adminRemark = $request->input('remark') ?? '-';
        $sheet->setCellValue('D30', $adminRemark);

        $filename = 'FD-F04-SELECTED-' . now()->format('Ymd-His') . '.xlsx';
        $filepath = storage_path("app/public/exports/{$filename}");

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($filepath);

        return response()->download($filepath)->deleteFileAfterSend(true);
    }

    /** Export Staff Request to Excel */
    public function exportToExcel(Request $request, $userId)
    {
        $staff = \App\Models\User::findOrFail($userId);

        $requests = LaptopRequest::with(['laptop', 'accessories'])
            ->where('user_id', $userId)
            ->whereIn('status', ['approved', 'completed'])
            ->get();

        $remark = $request->input('remark') ?? '-';

        $spreadsheet = IOFactory::load(storage_path('app/templates/PURCHASE REQUISITION FORM.xlsx'));
        $sheet = $spreadsheet->getActiveSheet();

        $startRow = 18;

        foreach ($requests as $index => $req) {
            $row = $startRow + $index;

            $accessoryText = $req->accessories->map(function ($a) {
                return $a->accessory_name . ' (x' . $a->quantity . ')';
            })->implode(', ');

            $item = $this->getExportItemDescription($req);
            $fullItem = $accessoryText ? $item . ", " . $accessoryText : $item;

            $sheet->setCellValue("B{$row}", $index + 1);
            $sheet->setCellValue("C{$row}", $fullItem);
            $sheet->setCellValue("J{$row}", $req->assigned_quantity ?? 1);
            $sheet->setCellValue("K{$row}", 'Unit');
        }

        $sheet->setCellValue('D30', $remark ?: '-');

        $filename = 'FD-F04-' . now()->format('Ymd-His') . '.xlsx';
        $filepath = storage_path("app/public/exports/{$filename}");

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($filepath);

        return response()->download($filepath)->deleteFileAfterSend(true);
    }

    protected function getLaptopSpecsText($request)
    {
        if ($request->type === 'new' && $request->laptop) {
            return $request->laptop->specs ?? '-';
        }

        return '-';
    }

    public function exportAllFiltered(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $start = Carbon::parse($request->start_date)->startOfDay();
        $end = Carbon::parse($request->end_date)->endOfDay();

        $query = LaptopRequest::with(['user', 'laptop', 'accessories'])
            ->whereBetween('created_at', [$start, $end])
            ->whereIn('status', ['approved', 'completed']);

        if ($request->filled('staff_ids')) {
            $query->whereIn('user_id', $request->staff_ids);
        }

        $requests = $query->get();

        if ($requests->isEmpty()) {
            return back()->with('error', 'No requests found for the selected range.');
        }

        // Group by specs
        $grouped = $requests->groupBy(function ($req) {
            return implode('|', [
                $req->type,
                $req->laptop?->brand,
                $req->laptop?->model,
                $req->assigned_part,
                $req->upgrade_type,
                $req->replacement_part,
            ]);
        });

        // Load template
        $spreadsheet = IOFactory::load(storage_path('app/templates/PURCHASE REQUISITION FORM.xlsx'));
        $sheet = $spreadsheet->getActiveSheet();

        $row = 18;
        $itemIndex = 1;

        $remark = $request->input('remark') ?? '-';


        foreach ($grouped as $group) {
            $first = $group->first();

            $staffNames = $group->pluck('user.name')->join(', ');

            $accessoryText = collect();
            foreach ($group as $req) {
                if ($req->accessories && $req->accessories->isNotEmpty()) {
                    $accessoryText = $accessoryText->merge($req->accessories->map(function ($a) {
                        return $a->accessory_name . ' (x' . $a->quantity . ')';
                    }));
                }
            }
            $accessoryStr = $accessoryText->unique()->implode(', ');

            $item = $this->getExportItemDescription($first);
            $fullItem = $accessoryStr ? "$item, $accessoryStr" : $item;

            // Format item row
            $staffNames = $group->pluck('user.name')->join(', ');
            $descriptionWithNames = "$item – $staffNames";

            // Add item line
            $sheet->setCellValue("B{$row}", $itemIndex);
            $sheet->setCellValue("C{$row}", $descriptionWithNames);
            $sheet->setCellValue("J{$row}", $group->count());
            $sheet->setCellValue("K{$row}", 'Unit');

            // Add second line: specification
            $sheet->setCellValue("C{$row}", $this->getLaptopSpecsText($first));
            $row++;
            $itemIndex++;
            
            //$remark .= "\nItem $itemIndex – Requested by: $staffNames";
        }

        // Push everything below down — insert space above row 30
        $sheet->insertNewRowBefore(30, $row - 30 + 3); // 3 buffer rows

        // Then drop the remark to new row
        $sheet->setCellValue('D' . ($row + 2), $remark);

        $filename = 'FD-F04-GROUPED-' . now()->format('Ymd-His') . '.xlsx';
        $filepath = storage_path("app/public/exports/{$filename}");

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($filepath);

        return response()->download($filepath)->deleteFileAfterSend(true);
    }

    /** Assign Part/Upgrade (Non-Laptop) */
    public function assignPartUpgradeForm($id)
    {
        $request = LaptopRequest::with(['user', 'assignedLaptop'])->findOrFail($id);

        if (($request->type === 'replacement' && $request->replacement_part !== 'Laptop') || $request->type === 'upgrade') {
            return view('admin.laptops.assign-part-upgrade', compact('request'));
        }

        return redirect()->route('admin.view-requests')->with('error', 'This request does not require a part/upgrade assignment.');
    }

    /** Store Assigned Part/Upgrade */
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

        return redirect()->route('admin.view-requests')->with('success', 'Part/Upgrade assigned and marked as completed.');
    }

    /** Get Export Description Text */
    protected function getExportItemDescription($request)
    {
        if ($request->type === 'new' && $request->laptop) {
            return $request->laptop->brand . ' ' . $request->laptop->model;
        } elseif ($request->type === 'replacement') {
            return ($request->assigned_part ?? $request->replacement_part ?? $request->other_replacement ?? '-') . ' (Replacement)';
        } elseif ($request->type === 'upgrade') {
            return ($request->assigned_part ?? $request->upgrade_type ?? '-') . ' (Upgrade)';
        }

        return '-';
    }
}