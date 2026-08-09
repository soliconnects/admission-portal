<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable; // not needed here, Teacher isn't the auth model

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'phone', 'subject_specialty'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedLeads()
    {
        return $this->hasMany(AdmissionLead::class, 'assigned_teacher_id');
    }
}