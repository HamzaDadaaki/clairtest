<?php

namespace App\Support;

use Illuminate\Support\Str;

class AnalyticsStore
{
    public static function record(array $event): array
    {
        $events = self::events();
        $normalized = self::normalizeEvent($event);
        $events[] = $normalized;
        JsonFileStore::write(self::storagePath(), $events);

        return $normalized;
    }

    public static function events(): array
    {
        return collect(JsonFileStore::read(self::storagePath(), []))
            ->filter(fn ($item) => is_array($item))
            ->map(fn (array $item) => self::normalizeEvent($item))
            ->sortByDesc('created_at')
            ->values()
            ->all();
    }

    public static function summary(): array
    {
        $events = self::events();
        $sessions = [];
        $pageViews = 0;
        $clicks = 0;
        $conversions = 0;
        $sourceBreakdown = [];
        $deviceBreakdown = [];
        $entryPages = [];
        $ctaBreakdown = [];

        foreach ($events as $event) {
            $sessionId = $event['session_id'];

            if (! isset($sessions[$sessionId])) {
                $sessions[$sessionId] = [
                    'session_id' => $sessionId,
                    'started_at' => $event['created_at'],
                    'last_seen_at' => $event['created_at'],
                    'entry_page' => $event['page'] ?: '/',
                    'source' => $event['source'],
                    'device' => $event['device_type'],
                    'page_views' => 0,
                    'clicks' => 0,
                    'conversions' => 0,
                    'max_duration_seconds' => 0,
                    'user_agent' => $event['user_agent'],
                    'country_hint' => $event['country_hint'],
                    'referrer' => $event['referrer'],
                ];
            }

            $sessions[$sessionId]['last_seen_at'] = max($sessions[$sessionId]['last_seen_at'], $event['created_at']);
            $sessions[$sessionId]['max_duration_seconds'] = max($sessions[$sessionId]['max_duration_seconds'], (int) $event['duration_seconds']);

            if ($event['event_type'] === 'page_view') {
                $sessions[$sessionId]['page_views']++;
                $pageViews++;
            }

            if ($event['event_type'] === 'click') {
                $sessions[$sessionId]['clicks']++;
                $clicks++;
                $label = $event['link_label'] ?: 'Unknown click';
                $ctaBreakdown[$label] = ($ctaBreakdown[$label] ?? 0) + 1;
            }

            if ($event['event_type'] === 'conversion') {
                $sessions[$sessionId]['conversions']++;
                $conversions++;
                $label = $event['conversion_label'] ?: 'Conversion';
                $ctaBreakdown[$label] = ($ctaBreakdown[$label] ?? 0) + 1;
            }
        }

        foreach ($sessions as $session) {
            $sourceBreakdown[$session['source']] = ($sourceBreakdown[$session['source']] ?? 0) + 1;
            $deviceBreakdown[$session['device']] = ($deviceBreakdown[$session['device']] ?? 0) + 1;
            $entryPages[$session['entry_page']] = ($entryPages[$session['entry_page']] ?? 0) + 1;
        }

        $visits = count($sessions);
        $bounces = count(array_filter($sessions, function (array $session) {
            return $session['page_views'] <= 1
                && $session['clicks'] === 0
                && $session['conversions'] === 0
                && $session['max_duration_seconds'] < 20;
        }));

        $convertingSessions = count(array_filter($sessions, fn (array $session) => $session['conversions'] > 0));

        $recentSessions = collect($sessions)
            ->sortByDesc('started_at')
            ->values()
            ->take(40)
            ->all();

        return [
            'visits' => $visits,
            'page_views' => $pageViews,
            'clicks' => $clicks,
            'conversions' => $conversions,
            'bounce_rate' => $visits > 0 ? round(($bounces / $visits) * 100, 1) : 0.0,
            'conversion_rate' => $visits > 0 ? round(($convertingSessions / $visits) * 100, 1) : 0.0,
            'source_breakdown' => self::sortBreakdown($sourceBreakdown),
            'device_breakdown' => self::sortBreakdown($deviceBreakdown),
            'entry_pages' => self::sortBreakdown($entryPages),
            'cta_breakdown' => self::sortBreakdown($ctaBreakdown),
            'recent_sessions' => $recentSessions,
            'recent_events' => array_slice($events, 0, 80),
        ];
    }

    protected static function sortBreakdown(array $breakdown): array
    {
        arsort($breakdown);

        return $breakdown;
    }

    protected static function normalizeEvent(array $event): array
    {
        $allowedTypes = ['page_view', 'click', 'conversion', 'session_summary'];
        $eventType = (string) ($event['event_type'] ?? 'page_view');

        if (! in_array($eventType, $allowedTypes, true)) {
            $eventType = 'page_view';
        }

        $source = trim((string) ($event['source'] ?? 'direct'));
        $source = $source !== '' ? Str::limit($source, 80, '') : 'direct';

        return [
            'id' => trim((string) ($event['id'] ?? '')) ?: (string) Str::uuid(),
            'session_id' => trim((string) ($event['session_id'] ?? '')) ?: (string) Str::uuid(),
            'event_type' => $eventType,
            'page' => trim((string) ($event['page'] ?? '/')) ?: '/',
            'referrer' => trim((string) ($event['referrer'] ?? '')),
            'source' => $source,
            'device_type' => trim((string) ($event['device_type'] ?? 'desktop')) ?: 'desktop',
            'device_os' => trim((string) ($event['device_os'] ?? '')),
            'screen_width' => (int) ($event['screen_width'] ?? 0),
            'screen_height' => (int) ($event['screen_height'] ?? 0),
            'link_url' => trim((string) ($event['link_url'] ?? '')),
            'link_label' => trim((string) ($event['link_label'] ?? '')),
            'conversion_label' => trim((string) ($event['conversion_label'] ?? '')),
            'duration_seconds' => (int) ($event['duration_seconds'] ?? 0),
            'user_agent' => trim((string) ($event['user_agent'] ?? '')),
            'country_hint' => trim((string) ($event['country_hint'] ?? '')),
            'ip' => trim((string) ($event['ip'] ?? '')),
            'created_at' => trim((string) ($event['created_at'] ?? now()->toDateTimeString())),
        ];
    }

    protected static function storagePath(): string
    {
        return storage_path('app/analytics_events.json');
    }
}
