<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdmissionLead;
use App\Models\AdmissionAuditLog;
use App\Models\ClassRoom;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Student;
use App\Models\ParentModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdmissionLeadController extends Controller
{
    public function index(Request $request)
    {
        $query = AdmissionLead::with(['import', 'assignedTeacher']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('student_name', 'like', "%{$search}%")
                  ->orWhere('parent_name', 'like', "%{$search}%")
                  ->orWhere('parent_phone', 'like', "%{$search}%");
            });
        }

        $leads = $query->latest()->paginate(15)->withQueryString();

        return view('admin.admission.leads.index', compact('leads'));
    }

    public function show(AdmissionLead $lead)
    {
        $lead->load(['import', 'assignedTeacher']);
        $classes = ClassRoom::orderBy('name')->get();
        $teachers = Teacher::orderBy('name')->get();

        return view('admin.admission.leads.show', compact('lead', 'classes', 'teachers'));
    }

    /**
     * Admin directly admits the student — creates User+Student+Parent records.
     */
    public function directAdmit(Request $request, AdmissionLead $lead)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
        ]);

        if (in_array($lead->status, ['admitted', 'rejected'])) {
            return back()->withErrors(['lead' => 'This lead has already been finalized.']);
        }

        DB::transaction(function () use ($request, $lead) {
            $student = $this->createStudentFromLead($lead, $request->class_id);

            $lead->update([
                'status' => 'admitted',
                'final_class_id' => $request->class_id,
                'decided_by' => auth()->id(),
                'decided_at' => now(),
                'resulting_student_id' => $student->id,
            ]);

            $class = $student->classRoom;

            AdmissionAuditLog::create([
                'lead_id' => $lead->id,
                'student_id' => $student->id,
                'student_name' => $lead->student_name,
                'dob' => $lead->dob,
                'gender' => $lead->gender,
                'parent_name' => $lead->parent_name,
                'parent_phone' => $lead->parent_phone,
                'applied_class_text' => $lead->applied_class_text,
                'class_id' => $class?->id,
                'class_name' => $class?->name,
                'class_section' => $class?->section,
                'admitted_by' => auth()->id(),
                'admitted_at' => now(),
                'admin_remarks' => $request->admin_remarks,
            ]);
        });

        return redirect()
            ->route('admin.admission.leads.index')
            ->with('success', "{$lead->student_name} has been admitted directly.");
    }

    /**
     * Admin forwards the lead to a teacher for assessment.
     */
    public function forward(Request $request, AdmissionLead $lead)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
        ]);

        if (in_array($lead->status, ['admitted', 'rejected'])) {
            return back()->withErrors(['lead' => 'This lead has already been finalized.']);
        }

        $lead->update([
            'assigned_teacher_id' => $request->teacher_id,
            'forwarded_by' => auth()->id(),
            'forwarded_at' => now(),
            'status' => 'forwarded',
        ]);

        return redirect()
            ->route('admin.admission.leads.index')
            ->with('success', "{$lead->student_name} has been forwarded to the teacher for assessment.");
    }

    /**
     * Shared logic: creates User+Student(+Parent) records from a lead.
     * Used by both direct admit and (later) approval-after-teacher-review.
     */
    private function createStudentFromLead(AdmissionLead $lead, int $classId): Student
    {
        // Create the student's own login (using a generated placeholder email if none captured)
        $studentUser = User::create([
            'name' => $lead->student_name,
            'email' => Str::slug($lead->student_name) . '.' . $lead->id . '@student.school.test',
            'password' => bcrypt(Str::random(12)),
        ]);
        $studentUser->assignRole('student');

        $student = Student::create([
            'user_id' => $studentUser->id,
            'name' => $lead->student_name,
            'dob' => $lead->dob,
            'gender' => $lead->gender,
            'address' => $lead->address,
            'class_id' => $classId,
            'admission_date' => now(),
            'status' => 'active',
        ]);

        // Create or reuse the parent account (match by phone to avoid duplicate parent logins)
        if ($lead->parent_phone) {
            $parentProfile = ParentModel::where('phone', $lead->parent_phone)->first();

            if (! $parentProfile) {
                $parentUser = User::create([
                    'name' => $lead->parent_name ?? 'Parent of ' . $lead->student_name,
                    'email' => $lead->parent_email ?: (Str::slug($lead->parent_name ?? 'parent') . '.' . $lead->id . '@parent.school.test'),
                    'password' => bcrypt(Str::random(12)),
                ]);
                $parentUser->assignRole('parent');

                $parentProfile = ParentModel::create([
                    'user_id' => $parentUser->id,
                    'name' => $lead->parent_name,
                    'phone' => $lead->parent_phone,
                    'email' => $lead->parent_email,
                ]);
            }

            $student->parents()->attach($parentProfile->id, ['relationship' => 'guardian']);
        }

        return $student;
    }
}