<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LanguageMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->has('lang')) {
            $lang = $request->get('lang');
            if (in_array($lang, ['fr', 'en', 'ar'])) {
                app()->setLocale($lang);
                session(['locale' => $lang]);
            }
        } elseif (session('locale')) {
            app()->setLocale(session('locale'));
        }

        return $next($request);
    }
}
