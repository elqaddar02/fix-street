<?php

use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Models\Report;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $latestReports = Report::with(['category', 'city'])
        ->latest()
        ->take(6)
        ->get();

    return view('home', compact('latestReports'));
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');

    Route::get('/admin/reports', [AdminReportController::class, 'index'])->name('admin.reports.index');
    Route::patch('/admin/reports/{report}/status', [AdminReportController::class, 'updateStatus'])->name('admin.reports.updateStatus');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
