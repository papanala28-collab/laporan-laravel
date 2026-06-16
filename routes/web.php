<?php

use App\Http\Controllers\ProjectAttendancePdfController;
use App\Http\Controllers\ReportPdfController;
use App\Livewire\Dashboard;
use App\Livewire\Projects\ProjectForm;
use App\Livewire\Projects\ProjectIndex;
use App\Livewire\Projects\ProjectShow;
use App\Livewire\Reports\ReportForm;
use App\Livewire\Reports\ReportIndex;
use App\Livewire\Reports\ReportPrint;
use App\Livewire\Reports\ReportShow;
use App\Livewire\Users\UserIndex;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::middleware('auth')->group(function () {
    Route::view('profile', 'profile')->name('profile');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', Dashboard::class)->name('dashboard');
    Route::get('users', UserIndex::class)->name('users.index');

    Route::get('projects', ProjectIndex::class)->name('projects.index');
    Route::get('projects/create', ProjectForm::class)->name('projects.create');
    Route::get('projects/{project}/attendance-pdf', ProjectAttendancePdfController::class)->name('projects.attendance-pdf');
    Route::get('projects/{project}', ProjectShow::class)->name('projects.show');
    Route::get('projects/{project}/edit', ProjectForm::class)->name('projects.edit');

    Route::get('reports', ReportIndex::class)->name('reports.index');
    Route::get('reports/create', ReportForm::class)->name('reports.create');
    Route::get('reports/{report}/pdf', ReportPdfController::class)->name('reports.pdf');
    Route::get('reports/{report}', ReportShow::class)->name('reports.show');
    Route::get('reports/{report}/edit', ReportForm::class)->name('reports.edit');
    Route::get('reports/{report}/print', ReportPrint::class)->name('reports.print');
});

require __DIR__.'/auth.php';
