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
        $middleware->web(\App\Http\Middleware\EnsureUserIsActive::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // An expired form token (e.g. a sign-in page left open, or a second
        // submit after the first one already signed you in) shouldn't end on a
        // bare "419 Page Expired": send people back with a clear message.
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return null;
            }

            if ($request->routeIs('logout')) {
                return redirect('/');
            }

            if ($request->user() && $request->routeIs('login', 'register')) {
                return redirect()->route($request->user()->is_admin ? 'admin.dashboard' : 'dashboard');
            }

            return redirect()->back()
                ->withInput($request->except(['password', 'password_confirmation', 'current_password', '_token']))
                ->with('status', __('signin.session_expired'))
                ->with('notice', __('signin.session_expired'));
        });
    })->create();
