{{--
    Plain-text alternative.

    Not a formality: a message with no text part scores worse with filters, and
    some recipients — and every text-only mail client — see only this version.
    So it carries the same words, the same opt-out and the same identity line
    rather than a one-line "view this in your browser".
--}}
@php
    $studio = $studio ?? config('mail.reply_to.name');
@endphp
{{ $bodyText }}

--
@if ($goal)
You are receiving this because {{ $prospect->company ?: 'your business' }} looked like a good fit for {{ $goal }}.
@else
You are receiving this because we thought it might be relevant to {{ $prospect->company ?: 'your business' }}.
@endif
Unsubscribe in one click: {{ $unsubscribeUrl }}
{{ $studio }}@if ($postalAddress) · {{ $postalAddress }}@endif
