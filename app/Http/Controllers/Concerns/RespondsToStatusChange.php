<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

/**
 * Shared reply for the admin status controls.
 *
 * The status widgets in the admin tables submit with fetch(), so they need a
 * JSON answer rather than a redirect. Falling back to the redirect keeps the
 * plain <form> submit working when JavaScript is unavailable.
 */
trait RespondsToStatusChange
{
    protected function statusResponse(Request $request, string $message, array $payload = []): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'message' => $message] + $payload);
        }

        return back()->with('success', $message);
    }

    /**
     * The change was refused (e.g. an admin trying to deactivate themselves).
     * The fetch() widgets roll back and show the reason; plain forms get a flash.
     */
    protected function statusRefused(Request $request, string $message): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['ok' => false, 'message' => $message], 422);
        }

        return back()->with('error', $message);
    }
}
