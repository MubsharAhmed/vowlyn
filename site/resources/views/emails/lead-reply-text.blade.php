{{-- Plain-text alternative. Text sent through e() cannot break the layout. --}}
@php
    $studio = (string) config('mail.reply_to.name') ?: config('app.name');
    $replyTo = (string) config('mail.reply_to.address');
@endphp
{{ $bodyText }}

—

Your original message:

{{ $contactRequest->brief }}

{{ $contactRequest->name }}@if ($contactRequest->company) · {{ $contactRequest->company }}@endif · {{ $contactRequest->serviceLabel() }}

Reply to this email and it comes straight back to us at {{ $replyTo }}.

{{ $studio }}
