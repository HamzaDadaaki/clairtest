<?php

namespace App\Http\Controllers;

use App\Support\AnalyticsStore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SiteAnalyticsController extends Controller
{
    public function track(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'session_id' => ['required', 'string', 'max:120'],
            'event_type' => ['required', 'string', 'max:40'],
            'page' => ['nullable', 'string', 'max:255'],
            'referrer' => ['nullable', 'string', 'max:500'],
            'source' => ['nullable', 'string', 'max:80'],
            'device_type' => ['nullable', 'string', 'max:40'],
            'device_os' => ['nullable', 'string', 'max:40'],
            'screen_width' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'screen_height' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'link_url' => ['nullable', 'string', 'max:500'],
            'link_label' => ['nullable', 'string', 'max:160'],
            'conversion_label' => ['nullable', 'string', 'max:160'],
            'duration_seconds' => ['nullable', 'integer', 'min:0', 'max:86400'],
            'country_hint' => ['nullable', 'string', 'max:120'],
        ]);

        AnalyticsStore::record([
            ...$payload,
            'user_agent' => (string) $request->userAgent(),
            'ip' => (string) $request->ip(),
            'created_at' => now()->toDateTimeString(),
        ]);

        return response()->json(['ok' => true]);
    }
}
