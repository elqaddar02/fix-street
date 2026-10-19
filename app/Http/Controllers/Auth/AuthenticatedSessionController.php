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

        // Admins always land on the admin dashboard, unless they were on their
        // way to another admin page. The citizen pages are not their workspace.
        if (Auth::user()->is_admin) {
            $intended = $request->session()->pull('url.intended');

            return is_string($intended) && str_starts_with($intended, url('/admin'))
                ? redirect()->to($intended)
                : redirect()->route('admin.dashboard');
        }

        // Citizens go back to where they were (e.g. a report they tried to confirm).
        if ($request->session()->has('url.intended')) {
            return redirect()->intended();
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
