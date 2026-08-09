<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\ParentPortal\DashboardController as ParentDashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {

    // Admin routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');

        Route::prefix('admission')->name('admission.')->group(function () {
            Route::get('/imports', [\App\Http\Controllers\Admin\AdmissionImportController::class, 'index'])->name('imports.index');
            Route::get('/imports/create', [\App\Http\Controllers\Admin\AdmissionImportController::class, 'create'])->name('imports.create');
            Route::post('/imports', [\App\Http\Controllers\Admin\AdmissionImportController::class, 'store'])->name('imports.store');

            Route::get('/leads', [\App\Http\Controllers\Admin\AdmissionLeadController::class, 'index'])->name('leads.index');
            Route::get('/leads/{lead}', [\App\Http\Controllers\Admin\AdmissionLeadController::class, 'show'])->name('leads.show');
            Route::post('/leads/{lead}/direct-admit', [\App\Http\Controllers\Admin\AdmissionLeadController::class, 'directAdmit'])->name('leads.direct-admit');
            Route::post('/leads/{lead}/forward', [\App\Http\Controllers\Admin\AdmissionLeadController::class, 'forward'])->name('leads.forward');
        });
    });

    // Teacher routes
    Route::middleware('role:teacher')->prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/dashboard', TeacherDashboardController::class)->name('dashboard');

        Route::prefix('admission')->name('admission.')->group(function () {
            Route::get('/leads', [\App\Http\Controllers\Teacher\AdmissionLeadController::class, 'index'])->name('leads.index');
            Route::get('/leads/{lead}', [\App\Http\Controllers\Teacher\AdmissionLeadController::class, 'show'])->name('leads.show');
            Route::post('/leads/{lead}/assess', [\App\Http\Controllers\Teacher\AdmissionLeadController::class, 'submitAssessment'])->name('leads.assess');
        });
    });

    // Student routes
    Route::middleware('role:student')->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', StudentDashboardController::class)->name('dashboard');
    });

    // Parent routes
    Route::middleware('role:parent')->prefix('parent')->name('parent.')->group(function () {
        Route::get('/dashboard', ParentDashboardController::class)->name('dashboard');
    });

    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';