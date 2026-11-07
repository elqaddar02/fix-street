<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Models\AdEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdClickController extends Controller
{
    public function track(Request $request, Ad $ad)
    {
        if ($ad->isCurrentlyActive()) {
            $ad->events()->create([
                'type' => AdEvent::TYPE_CLICK,
                'user_id' => Auth::id(),
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
            ]);
        }

        $targetUrl = $ad->target_url ?: url('/');

        return redirect()->away($targetUrl);
    }
}
