<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionImport extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename', 'uploaded_by', 'total_rows',
        'new_leads_count', 'duplicate_count', 'status',
    ];

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function leads()
    {
        return $this->hasMany(AdmissionLead::class, 'import_id');
    }
}