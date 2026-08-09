<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('admission_imports', function (Blueprint $table) {
        $table->id();
        $table->string('filename');
        $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
        $table->unsignedInteger('total_rows')->default(0);
        $table->unsignedInteger('new_leads_count')->default(0);
        $table->unsignedInteger('duplicate_count')->default(0);
        $table->string('status')->default('processing'); // processing/completed/failed
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('admission_imports');
}
};
