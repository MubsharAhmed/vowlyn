@php
    $ga4MeasurementId = strtoupper(trim((string) config('services.google_analytics.measurement_id')));
    $hasGoogleAnalytics = preg_match('/\AG-[A-Z0-9]+\z/', $ga4MeasurementId) === 1;
    $analyticsEvent = session('analytics.event');
@endphp

@if ($hasGoogleAnalytics)
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ rawurlencode($ga4MeasurementId) }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', @json($ga4MeasurementId));
        @if (is_array($analyticsEvent) && isset($analyticsEvent['event']))
            gtag('event', @json($analyticsEvent['event']), @json(collect($analyticsEvent)->except('event')->all()));
        @endif
    </script>
@endif
