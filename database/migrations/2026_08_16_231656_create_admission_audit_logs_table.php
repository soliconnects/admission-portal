<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admission_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->nullable()->constrained('admission_leads')->nullOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();

            // Snapshot of the admitted candidate
            $table->string('student_name');
            $table->date('dob')->nullable();
            $table->string('gender')->nullable();
            $table->string('parent_name')->nullable();
            $table->string('parent_phone')->nullable();
            $table->string('applied_class_text')->nullable();

            // Snapshot of the assigned class at time of admission
            $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->string('class_name')->nullable();
            $table->string('class_section')->nullable();

            // Who / when / remarks
            $table->foreignId('admitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('admitted_at')->nullable();
            $table->text('admin_remarks')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admission_audit_logs');
    }
};
