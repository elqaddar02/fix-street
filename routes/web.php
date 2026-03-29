<?php

use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Models\Report;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $latestReports = Report::with(['category', 'city', 'user', 'comments.user'])
        ->latest()
        ->take(6)
        ->get();

    $reportsJson = json_encode($latestReports->map(function ($report) {
        return [
            'id' => $report->id,
            'title' => $report->title,
            'description' => $report->description,
            'category' => optional($report->category)->name ?? 'Unknown',
            'city' => optional($report->city)->name ?? 'Unknown',
            'quartier' => optional($report->quartier)->name ?? null,
            'status' => $report->status,
            'user' => optional($report->user)->name ?? 'Anonymous',
            'created_at' => $report->created_at->format('M j, Y'),
            'comments' => $report->comments->map(function ($comment) {
                return [
                    'user' => optional($comment->user)->name ?? 'Anonymous',
                    'comment' => $comment->comment,
                    'created_at' => $comment->created_at->diffForHumans(),
                ];
            })->toArray(),
        ];
    })->toArray());

    $isAuthenticated = auth()->check() ? 'true' : 'false';

    return view('home', compact('latestReports', 'reportsJson', 'isAuthenticated'));
});

Route::view('/about', 'about')->name('about');
Route::view('/contact', 'contact')->name('contact');
Route::view('/privacy', 'privacy')->name('privacy');
Route::view('/terms', 'terms')->name('terms');
Route::view('/help', 'help')->name('help');

Route::get('/login-redirect', function () {
    session(['url.intended' => url()->current()]);
    return redirect()->route('login');
})->name('login.redirect');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    Route::post('/reports/{report}/comments', [ReportController::class, 'storeComment'])->name('reports.comments.store');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });

    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');
    Route::patch('/users/{user}/status', [\App\Http\Controllers\Admin\UserController::class, 'updateStatus'])->name('users.updateStatus');
    Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/{report}', [\App\Http\Controllers\Admin\ReportController::class, 'show'])->name('reports.show');
    Route::patch('/reports/{report}/status', [\App\Http\Controllers\Admin\ReportController::class, 'updateStatus'])->name('reports.updateStatus');
    Route::delete('/reports/{report}', [\App\Http\Controllers\Admin\ReportController::class, 'destroy'])->name('reports.destroy');

    Route::get('/cities', [\App\Http\Controllers\Admin\CityController::class, 'index'])->name('cities.index');
    Route::post('/cities', [\App\Http\Controllers\Admin\CityController::class, 'store'])->name('cities.store');
    Route::get('/cities/{city}/edit', [\App\Http\Controllers\Admin\CityController::class, 'edit'])->name('cities.edit');
    Route::patch('/cities/{city}', [\App\Http\Controllers\Admin\CityController::class, 'update'])->name('cities.update');
    Route::delete('/cities/{city}', [\App\Http\Controllers\Admin\CityController::class, 'destroy'])->name('cities.destroy');
});

// Public report details (no login required)
Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
 Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

require __DIR__.'/auth.php';
