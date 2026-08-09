<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('admission_leads', function (Blueprint $table) {
        $table->id();
        $table->foreignId('import_id')->nullable()->constrained('admission_imports')->nullOnDelete();

        // Raw sheet data
        $table->string('student_name');
        $table->date('dob')->nullable();
        $table->string('gender')->nullable();
        $table->string('applied_class_text')->nullable();
        $table->string('parent_name')->nullable();
        $table->string('parent_phone')->nullable();
        $table->string('parent_email')->nullable();
        $table->string('address')->nullable();
        $table->string('previous_school')->nullable();
        $table->string('lead_source')->nullable();

        // Duplicate tracking
        $table->boolean('is_duplicate')->default(false);
        $table->foreignId('duplicate_of_student_id')->nullable()->constrained('students')->nullOnDelete();

        // Status & workflow
        $table->string('status')->default('new');
        // new, duplicate, forwarded, teacher_reviewed, pending_approval, admitted, rejected

        $table->foreignId('assigned_teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
        $table->foreignId('forwarded_by')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamp('forwarded_at')->nullable();

        $table->foreignId('recommended_class_id')->nullable()->constrained('classes')->nullOnDelete();
        $table->text('teacher_remarks')->nullable();
        $table->timestamp('teacher_reviewed_at')->nullable();

        $table->foreignId('final_class_id')->nullable()->constrained('classes')->nullOnDelete();
        $table->text('admin_remarks')->nullable();
        $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamp('decided_at')->nullable();

        $table->foreignId('resulting_student_id')->nullable()->constrained('students')->nullOnDelete();

        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('admission_leads');
}
};
