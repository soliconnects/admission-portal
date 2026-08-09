<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AdmissionLead;
use App\Models\ClassRoom;
use Illuminate\Http\Request;

class AdmissionLeadController extends Controller
{
    public function index(Request $request)
    {
        $teacher = auth()->user()->teacher;

        if (! $teacher) {
            abort(403, 'No teacher profile linked to this account.');
        }

        $leads = AdmissionLead::where('assigned_teacher_id', $teacher->id)
            ->where('status', 'forwarded')
            ->latest('forwarded_at')
            ->paginate(15);

        return view('teacher.admission.leads.index', compact('leads'));
    }

    public function show(AdmissionLead $lead)
    {
        $teacher = auth()->user()->teacher;

        // Ensure this teacher can only see leads assigned to them
        if ($lead->assigned_teacher_id !== $teacher->id) {
            abort(403, 'This lead is not assigned to you.');
        }

        $classes = ClassRoom::orderBy('name')->get();

        return view('teacher.admission.leads.show', compact('lead', 'classes'));
    }

    public function submitAssessment(Request $request, AdmissionLead $lead)
    {
        $teacher = auth()->user()->teacher;

        if ($lead->assigned_teacher_id !== $teacher->id) {
            abort(403, 'This lead is not assigned to you.');
        }

        if ($lead->status !== 'forwarded') {
            return back()->withErrors(['lead' => 'This lead is not in a forwarded state.']);
        }

        $request->validate([
            'recommended_class_id' => 'required|exists:classes,id',
            'teacher_remarks' => 'required|string|max:2000',
        ]);

        $lead->update([
            'recommended_class_id' => $request->recommended_class_id,
            'teacher_remarks' => $request->teacher_remarks,
            'teacher_reviewed_at' => now(),
            'status' => 'pending_approval',
        ]);

        return redirect()
            ->route('teacher.admission.leads.index')
            ->with('success', "Assessment submitted for {$lead->student_name}. Awaiting admin approval.");
    }
}