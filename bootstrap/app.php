<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(\App\Http\Middleware\SetLocale::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // A CSRF token mismatch (expired/stale session) normally renders Laravel's
        // bare "419 | Page Expired" page with no explanation. Instead, send the
        // user back to the form they were on with a clear, translatable message
        // and their input preserved (minus password fields).
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            $message = __('Your session has expired. Please try again.');

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 419);
            }

            return redirect()->back()
                ->withInput($request->except(['password', 'password_confirmation', 'current_password']))
                ->with('error', $message);
        });
    })->create();
