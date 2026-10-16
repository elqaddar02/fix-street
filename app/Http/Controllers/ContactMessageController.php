<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

/**
 * The Help page's "Contact us" form. It used to post to "#", which the
 * server rejected, so nothing a citizen wrote ever reached anyone.
 */
class ContactMessageController extends Controller
{
    public const TOPICS = ['platform', 'feedback', 'authority', 'report', 'not-fixed'];

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'topic' => ['required', Rule::in(self::TOPICS)],
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:5000'],
            // Honeypot: hidden from people, filled in by bots.
            'website' => ['prohibited'],
        ]);

        $to = config('app.contact_email');

        if (! $to) {
            return back()->withInput()->withErrors(['message' => __('help.contact.unavailable')]);
        }

        $body = implode("\n", [
            'Topic: ' . $data['topic'],
            'From: ' . $data['name'] . ' <' . $data['email'] . '>',
            'Phone: ' . (($data['phone'] ?? null) ?: '-'),
            '',
            $data['message'],
        ]);

        Mail::raw($body, function ($mail) use ($to, $data) {
            $mail->to($to)
                ->replyTo($data['email'], $data['name'])
                ->subject('[Madinup] ' . $data['subject']);
        });

        return redirect()->route('help', ['tab' => 'contact'])->with('success', __('help.contact.sent'));
    }
}
