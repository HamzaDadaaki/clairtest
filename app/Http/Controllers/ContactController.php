<?php

namespace App\Http\Controllers;

use App\Support\AnalyticsStore;
use App\Support\ContactInbox;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160'],
            'phone' => ['nullable', 'string', 'max:50'],
            'subject' => ['required', 'string', 'max:160'],
            'message' => ['required', 'string', 'max:2000'],
            'analytics_session_id' => ['nullable', 'string', 'max:120'],
            'analytics_source' => ['nullable', 'string', 'max:80'],
        ]);

        ContactInbox::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? '',
            'subject' => $data['subject'],
            'message' => $data['message'],
            'created_at' => now()->toDateTimeString(),
            'ip' => $request->ip(),
            'source' => $data['analytics_source'] ?? 'website form',
            'status' => 'new',
        ]);

        if (! empty($data['analytics_session_id'])) {
            AnalyticsStore::record([
                'session_id' => $data['analytics_session_id'],
                'event_type' => 'conversion',
                'page' => '/contact',
                'referrer' => (string) url()->previous(),
                'source' => $data['analytics_source'] ?? 'direct',
                'device_type' => $request->header('Sec-CH-UA-Mobile') === '?1' ? 'phone' : 'desktop',
                'conversion_label' => 'Contact form submit',
                'user_agent' => (string) $request->userAgent(),
                'ip' => (string) $request->ip(),
                'created_at' => now()->toDateTimeString(),
            ]);
        }

        return back()->with('success', 'Thank you. Your message has been saved successfully.');
    }
}
