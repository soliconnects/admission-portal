<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('students', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->string('name');
        $table->date('dob')->nullable();
        $table->string('gender')->nullable();
        $table->string('address')->nullable();
        $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete();
        $table->date('admission_date')->nullable();
        $table->string('status')->default('active'); // active/inactive
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('students');
}
};
