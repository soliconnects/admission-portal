<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionLead extends Model
{
    use HasFactory;

    protected $fillable = [
        'import_id', 'student_name', 'dob', 'gender', 'applied_class_text',
        'parent_name', 'parent_phone', 'parent_email', 'address',
        'previous_school', 'lead_source',
        'is_duplicate', 'duplicate_of_student_id',
        'status',
        'assigned_teacher_id', 'forwarded_by', 'forwarded_at',
        'recommended_class_id', 'teacher_remarks', 'teacher_reviewed_at',
        'final_class_id', 'admin_remarks', 'decided_by', 'decided_at',
        'resulting_student_id',
    ];

    protected $casts = [
        'is_duplicate' => 'boolean',
        'dob' => 'date',
        'forwarded_at' => 'datetime',
        'teacher_reviewed_at' => 'datetime',
        'decided_at' => 'datetime',
    ];

    public function import()
    {
        return $this->belongsTo(AdmissionImport::class, 'import_id');
    }

    public function assignedTeacher()
    {
        return $this->belongsTo(Teacher::class, 'assigned_teacher_id');
    }

    public function recommendedClass()
    {
        return $this->belongsTo(ClassRoom::class, 'recommended_class_id');
    }

    public function finalClass()
    {
        return $this->belongsTo(ClassRoom::class, 'final_class_id');
    }

    public function duplicateOfStudent()
    {
        return $this->belongsTo(Student::class, 'duplicate_of_student_id');
    }

    public function resultingStudent()
    {
        return $this->belongsTo(Student::class, 'resulting_student_id');
    }

    public function forwardedBy()
    {
        return $this->belongsTo(User::class, 'forwarded_by');
    }

    public function decidedBy()
    {
        return $this->belongsTo(User::class, 'decided_by');
    }
}