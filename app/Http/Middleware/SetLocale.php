<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale') ?? $request->cookie('locale') ?? app()->getLocale() ?? config('app.locale');
        
        if (in_array($locale, ['en', 'fr', 'ar'])) {
            app()->setLocale($locale);
        }
        
        return $next($request);
    }
}
