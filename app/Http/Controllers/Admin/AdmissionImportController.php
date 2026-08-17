<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\AdmissionLeadsImport;
use App\Models\AdmissionImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AdmissionImportController extends Controller
{
    public function index()
    {
        $imports = AdmissionImport::latest()->paginate(10);
        return view('admin.admission.imports.index', compact('imports'));
    }

    public function create()
    {
        return view('admin.admission.imports.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
    ]);

    $import = AdmissionImport::create([
        'filename' => $request->file('file')->getClientOriginalName(),
        'uploaded_by' => auth()->id(),
        'status' => 'processing',
    ]);

    $importer = new AdmissionLeadsImport($import);

    try {
        Excel::import($importer, $request->file('file'));

        $import->update([
            'total_rows' => $importer->newLeadsCount,
            'new_leads_count' => $importer->newLeadsCount,
            'duplicate_count' => 0,
            'status' => 'completed',
        ]);

        // NEW: surface any skipped-row validation failures
        $failures = $importer->failures();
        if ($failures->isNotEmpty()) {
            $messages = $failures->map(function ($failure) {
                return "Row {$failure->row()}: " . implode(', ', $failure->errors());
            })->implode(' | ');

            return redirect()
                ->route('admin.admission.leads.index')
                ->with('warning', "Import completed, but some rows were skipped: {$messages}");
        }

        return redirect()
            ->route('admin.admission.leads.index')
            ->with('success', "Import complete: {$importer->newLeadsCount} new leads imported.");
    } catch (\Throwable $e) {
        $import->update(['status' => 'failed']);
        return back()->withErrors(['file' => 'Import failed: ' . $e->getMessage()]);
    }
}
}