<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LaptopRequest;
use App\Models\User;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;

class ExportController extends Controller
{
    /**
     * Show export form with staff filter and date filter.
     */
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

            if ($request->filled('staff_ids') && !in_array('all', $request->staff_ids)) {
                $query->whereIn('user_id', $request->staff_ids);
            }

            $requests = $query->get();
        }

        return view('admin.laptops.export-request', [
            'staffList' => $staffList,
            'requests' => $requests,
        ]);
    }

    /**
     * Export selected requests from checkboxes.
     */
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

        return $this->generateExportFile($requests, $request->input('remark'), 'SELECTED');
    }

    /**
     * Export all filtered requests by date and user(s).
     */
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

        return $this->generateExportFile($requests, $request->input('remark'), 'GROUPED');
    }

    /**
     * Export individual staff request.
     */
    public function exportToExcel(Request $request, $userId)
    {
        $staff = User::findOrFail($userId);

        $requests = LaptopRequest::with(['laptop', 'accessories'])
            ->where('user_id', $userId)
            ->whereIn('status', ['approved', 'completed'])
            ->get();

        return $this->generateExportFile($requests, $request->input('remark'), "STAFF-{$staff->name}");
    }

    /**
     * Generate export file and return download.
     */
    protected function generateExportFile($requests, $remark = '-', $label = 'EXPORT')
    {
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

        $startRow = 18;
        $row = $startRow;
        $itemIndex = 1;
        $requiredRows = count($grouped) * 2;

        if ($requiredRows > (30 - $startRow)) {
            $sheet->insertNewRowBefore(30, $requiredRows - (30 - $startRow));
        }

        foreach ($grouped as $group) {
            $first = $group->first();
            $staffNames = $group->pluck('user.name')->join(', ');

            $accessoryText = collect();
            foreach ($group as $req) {
                if ($req->accessories && $req->accessories->isNotEmpty()) {
                    $accessoryText = $accessoryText->merge($req->accessories->map(fn($a) => "{$a->accessory_name} (x{$a->quantity})"));
                }
            }

            $accessoryStr = $accessoryText->unique()->implode(', ');
            $item = $this->getExportItemDescription($first);
            $description = trim($item . ($accessoryStr ? ", $accessoryStr" : '')) . " – $staffNames";

            // Row 1
            $sheet->setCellValue("B{$row}", $itemIndex);
            $sheet->mergeCells("C{$row}:I{$row}");
            $sheet->setCellValue("C{$row}", $description);
            $sheet->getStyle("C{$row}:I{$row}")->getAlignment()->setWrapText(true);
            $sheet->getStyle("C{$row}:I{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
            $sheet->getStyle("C{$row}:I{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->setCellValue("J{$row}", $group->count());
            $sheet->setCellValue("K{$row}", 'Unit');

            // Row 2
            $row++;
            $sheet->mergeCells("C{$row}:I{$row}");
            $sheet->setCellValue("C{$row}", $this->getLaptopSpecsText($first));
            $sheet->getStyle("C{$row}:I{$row}")->getAlignment()->setWrapText(true);
            $sheet->getStyle("C{$row}:I{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
            $sheet->getStyle("C{$row}:I{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

            $row++;
            $itemIndex++;
        }

        // Find remarks row (B30 to B60)
        $remarkRow = null;
        foreach (range(30, 60) as $r) {
            $val = strtoupper(trim((string) $sheet->getCell("B{$r}")->getValue()));
            if (str_contains($val, 'REMARK')) {
                $remarkRow = $r;
                break;
            }
        }

        if ($remarkRow) {
            $mergeRange = "D{$remarkRow}:K" . ($remarkRow + 1);

            if (!array_key_exists($mergeRange, $sheet->getMergeCells())) {
                $sheet->mergeCells($mergeRange);
            }

            $sheet->setCellValue("D{$remarkRow}", preg_replace('/[[:cntrl:]]/', '', $remark) ?: '-');
            $style = $sheet->getStyle("D{$remarkRow}");
            $style->getAlignment()->setWrapText(true);
            $style->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
            $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        }

        $filename = "FD-F04-{$label}-" . now()->format('Ymd-His') . '.xlsx';
        $filepath = storage_path("app/public/exports/{$filename}");

        IOFactory::createWriter($spreadsheet, 'Xlsx')->save($filepath);
        return response()->download($filepath)->deleteFileAfterSend(true);
    }

    /**
     * Get description for export rows.
     */
    protected function getExportItemDescription($request)
    {
        return match ($request->type) {
            'new' => $request->laptop?->brand . ' ' . $request->laptop?->model,
            'replacement' => ($request->assigned_part ?? $request->replacement_part ?? $request->other_replacement ?? '-') . ' (Replacement)',
            'upgrade' => ($request->assigned_part ?? $request->upgrade_type ?? '-') . ' (Upgrade)',
            default => '-',
        };
    }

    /**
     * Get laptop specs for grouped export.
     */
    protected function getLaptopSpecsText($request)
    {
        return ($request->type === 'new' && $request->laptop)
            ? $request->laptop->specs ?? '-'
            : '-';
    }
}
