<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id', 'student_id',
        'student_name', 'dob', 'gender', 'parent_name', 'parent_phone',
        'applied_class_text',
        'class_id', 'class_name', 'class_section',
        'admitted_by', 'admitted_at', 'admin_remarks',
    ];

    protected $casts = [
        'dob' => 'date',
        'admitted_at' => 'datetime',
    ];

    public function lead()
    {
        return $this->belongsTo(AdmissionLead::class, 'lead_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function admittedBy()
    {
        return $this->belongsTo(User::class, 'admitted_by');
    }
}
