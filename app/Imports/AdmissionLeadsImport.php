<?php

namespace App\Imports;

use App\Models\AdmissionLead;
use App\Models\AdmissionImport;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class AdmissionLeadsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    protected AdmissionImport $import;
    public int $newLeadsCount = 0;

    protected array $seenInFile = [];

    public function __construct(AdmissionImport $import)
    {
        $this->import = $import;
    }

    public function model(array $row)
    {
        $dob = !empty($row['date_of_birth']) && is_numeric($row['date_of_birth'])
            ? ExcelDate::excelToDateTimeObject($row['date_of_birth'])
            : (!empty($row['date_of_birth']) ? Carbon::parse($row['date_of_birth']) : null);

        if ($this->isDuplicate($row, $dob)) {
            return null;
        }

        $this->newLeadsCount++;

        return new AdmissionLead([
            'import_id' => $this->import->id,
            'student_name' => $row['student_name'] ?? null,
            'dob' => $dob,
            'gender' => $row['gender'] ?? null,
            'applied_class_text' => $row['applied_classgrade'] ?? null,
            'parent_name' => $row['parent_name'] ?? null,
            'parent_phone' => isset($row['parent_phone']) ? (string) $row['parent_phone'] : null,
            'parent_email' => $row['parent_email'] ?? null,
            'address' => $row['address'] ?? null,
            'previous_school' => $row['previous_school'] ?? null,
            'lead_source' => $row['lead_source'] ?? null,
            'status' => 'new',
        ]);
    }

    public function rules(): array
    {
        return [
            'student_name' => 'required|string|max:255',
            'parent_phone' => 'required',
        ];
    }

    protected function isDuplicate(array $row, ?\DateTime $dob): bool
    {
        $phone = isset($row['parent_phone']) ? (string) $row['parent_phone'] : null;

        $matches = function ($query) use ($row, $dob, $phone) {
            return $query->where('student_name', $row['student_name'])
                ->when($dob, fn($q) => $q->whereDate('dob', $dob))
                ->where('parent_phone', $phone);
        };

        $existsAsStudent = Student::where('name', $row['student_name'])
            ->when($dob, fn($q) => $q->whereDate('dob', $dob))
            ->whereHas('parents', fn($q) => $q->where('phone', $phone))
            ->exists();

        if ($existsAsStudent) {
            return true;
        }

        if ($matches(AdmissionLead::query())->exists()) {
            return true;
        }

        $key = strtolower(trim((string) $row['student_name'])) . '|' . $phone . '|' . ($dob ? $dob->format('Y-m-d') : '');

        if (isset($this->seenInFile[$key])) {
            return true;
        }

        $this->seenInFile[$key] = true;

        return false;
    }
}