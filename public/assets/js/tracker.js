(() => {
    const endpoint = document.documentElement.dataset.analyticsEndpoint;
    if (!endpoint) return;

    const storageKey = 'afayar_analytics_session_id';
    const startedKey = 'afayar_analytics_started_at';
    const sourceKey = 'afayar_analytics_source';
    const page = window.location.pathname + window.location.search;
    const referrer = document.referrer || '';

    const getSessionId = () => {
        let value = window.sessionStorage.getItem(storageKey);
        if (!value) {
            value = `${Date.now()}-${Math.random().toString(36).slice(2, 12)}`;
            window.sessionStorage.setItem(storageKey, value);
        }
        return value;
    };

    const getStartedAt = () => {
        let value = window.sessionStorage.getItem(startedKey);
        if (!value) {
            value = String(Date.now());
            window.sessionStorage.setItem(startedKey, value);
        }
        return Number(value);
    };

    const parseSource = () => {
        const params = new URLSearchParams(window.location.search);
        const utmSource = params.get('utm_source');
        if (utmSource) return utmSource.toLowerCase();

        if (!referrer) return 'direct';

        const lower = referrer.toLowerCase();
        if (lower.includes('instagram')) return 'instagram';
        if (lower.includes('facebook')) return 'facebook';
        if (lower.includes('youtube')) return 'youtube';
        if (lower.includes('whatsapp')) return 'whatsapp';
        if (lower.includes('tiktok')) return 'tiktok';
        if (lower.includes('google')) return 'google';
        if (lower.includes('bing')) return 'bing';

        try {
            return new URL(referrer).hostname.replace(/^www\./, '') || 'referral';
        } catch {
            return 'referral';
        }
    };

    const getSource = () => {
        let value = window.sessionStorage.getItem(sourceKey);
        if (!value) {
            value = parseSource();
            window.sessionStorage.setItem(sourceKey, value);
        }
        return value;
    };

    const getDeviceType = () => {
        const width = window.innerWidth || screen.width || 0;
        if (width <= 768) return 'phone';
        if (width <= 1024) return 'tablet';
        return 'desktop';
    };

    const getDeviceOS = () => {
        const ua = navigator.userAgent.toLowerCase();
        if (ua.includes('android')) return 'android';
        if (ua.includes('iphone') || ua.includes('ipad') || ua.includes('ios')) return 'ios';
        if (ua.includes('windows')) return 'windows';
        if (ua.includes('mac os')) return 'macos';
        if (ua.includes('linux')) return 'linux';
        return 'other';
    };

    const basePayload = () => ({
        session_id: getSessionId(),
        page,
        referrer,
        source: getSource(),
        device_type: getDeviceType(),
        device_os: getDeviceOS(),
        screen_width: window.innerWidth || screen.width || 0,
        screen_height: window.innerHeight || screen.height || 0,
        country_hint: Intl.DateTimeFormat().resolvedOptions().timeZone || '',
    });

    const send = (payload, useBeacon = false) => {
        const body = JSON.stringify({ ...basePayload(), ...payload });

        if (useBeacon && navigator.sendBeacon) {
            try {
                const blob = new Blob([body], { type: 'application/json' });
                navigator.sendBeacon(endpoint, blob);
                return;
            } catch {
                // fall through to fetch
            }
        }

        fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body,
            keepalive: true,
        }).catch(() => {});
    };

    send({ event_type: 'page_view' });

    const labelFromElement = (element) => {
        const explicit = element.getAttribute('data-analytics-label');
        if (explicit) return explicit;
        const text = (element.textContent || '').trim().replace(/\s+/g, ' ');
        if (text) return text.slice(0, 120);
        const aria = element.getAttribute('aria-label');
        if (aria) return aria.slice(0, 120);
        const href = element.getAttribute('href');
        if (href) return href.slice(0, 120);
        return 'Click';
    };

    document.addEventListener('click', (event) => {
        const target = event.target.closest('a, button');
        if (!target) return;

        const href = target.getAttribute('href') || '';
        const label = labelFromElement(target);
        const lower = href.toLowerCase();

        let eventType = 'click';
        let conversionLabel = '';

        if (lower.startsWith('https://wa.me') || lower.includes('whatsapp')) {
            eventType = 'conversion';
            conversionLabel = 'WhatsApp click';
        } else if (lower.startsWith('mailto:')) {
            eventType = 'conversion';
            conversionLabel = 'Email click';
        } else if (lower.startsWith('tel:')) {
            eventType = 'conversion';
            conversionLabel = 'Phone click';
        }

        send({
            event_type: eventType,
            link_url: href,
            link_label: label,
            conversion_label: conversionLabel,
        });
    }, { passive: true });

    const contactForm = document.querySelector('[data-contact-form]');
    const sessionInput = document.querySelector('[data-analytics-session]');
    const sourceInput = document.querySelector('[data-analytics-source]');
    if (sessionInput) sessionInput.value = getSessionId();
    if (sourceInput) sourceInput.value = getSource();
    const flushSummary = () => {
        const durationSeconds = Math.max(0, Math.round((Date.now() - getStartedAt()) / 1000));
        send({
            event_type: 'session_summary',
            duration_seconds: durationSeconds,
        }, true);
    };

    window.addEventListener('pagehide', flushSummary);
})();
