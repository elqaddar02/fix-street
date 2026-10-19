<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Deactivating an account in the admin used to change nothing: the flag was
 * saved but never read. A deactivated user who is still signed in is now
 * signed out on their next request.
 */
class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Strictly false: a model built in memory may not have loaded the column default yet.
        if ($user && $user->active === false) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json(['message' => __('signin.account_disabled')], 403);
            }

            return redirect()->route('login')->withErrors(['email' => __('signin.account_disabled')]);
        }

        return $next($request);
    }
}
