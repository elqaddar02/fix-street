<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Check if there's an intended URL (like from trying to like a report)
        if ($request->session()->has('url.intended')) {
            return redirect()->intended();
        }

        if (Auth::user()->is_admin) {
            if (\Illuminate\Support\Facades\Route::has('admin.dashboard')) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->to(url('/admin/dashboard'));
        }

        if (\Illuminate\Support\Facades\Route::has('dashboard')) {
            return redirect()->route('dashboard');
        }

        return redirect()->to(url('/dashboard'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
