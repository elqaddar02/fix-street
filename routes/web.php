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
            'category' => optional($report->category)->display_name ?? 'Unknown',
            'city' => optional($report->city)->display_name ?? 'Unknown',
            'quartier' => optional($report->quartier)->display_name ?? null,
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
})->name('home');

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
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
    Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
    
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
        Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
    Route::get('/reports/{report}', [\App\Http\Controllers\Admin\ReportController::class, 'show'])->name('reports.show');


    Route::patch('/reports/{report}/status', [\App\Http\Controllers\Admin\ReportController::class, 'updateStatus'])->name('reports.updateStatus');
    Route::delete('/reports/{report}', [\App\Http\Controllers\Admin\ReportController::class, 'destroy'])->name('reports.destroy');

    Route::get('/cities', [\App\Http\Controllers\Admin\CityController::class, 'index'])->name('cities.index');
    Route::post('/cities', [\App\Http\Controllers\Admin\CityController::class, 'store'])->name('cities.store');
    Route::get('/cities/{city}/edit', [\App\Http\Controllers\Admin\CityController::class, 'edit'])->name('cities.edit');
    Route::patch('/cities/{city}', [\App\Http\Controllers\Admin\CityController::class, 'update'])->name('cities.update');
    Route::delete('/cities/{city}', [\App\Http\Controllers\Admin\CityController::class, 'destroy'])->name('cities.destroy');

    Route::get('/quartiers', [\App\Http\Controllers\Admin\QuartierController::class, 'index'])->name('quartiers.index');
    Route::post('/quartiers', [\App\Http\Controllers\Admin\QuartierController::class, 'store'])->name('quartiers.store');
    Route::get('/quartiers/{quartier}/edit', [\App\Http\Controllers\Admin\QuartierController::class, 'edit'])->name('quartiers.edit');
    Route::patch('/quartiers/{quartier}', [\App\Http\Controllers\Admin\QuartierController::class, 'update'])->name('quartiers.update');
    Route::delete('/quartiers/{quartier}', [\App\Http\Controllers\Admin\QuartierController::class, 'destroy'])->name('quartiers.destroy');
});

Route::get('/api/quartiers/{city}', function (\App\Models\City $city) {
    return $city->quartiers()->where('active', true)->get()->map(function ($quartier) {
        return [
            'id' => $quartier->id,
            'name' => $quartier->display_name,
        ];
    });
});

// Language switcher
Route::get('/language/{language}', [App\Http\Controllers\LanguageController::class, 'switchLanguage'])->name('language.switch');

require __DIR__.'/auth.php';
