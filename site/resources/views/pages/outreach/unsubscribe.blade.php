@extends('layouts.app')

@section('title', 'Unsubscribe from Vowlyn email')
@section('description', 'Remove your address from Vowlyn outreach email. One click, no questions, no follow-up.')
@section('canonical', route('outreach.unsubscribe', ['token' => $prospect->unsubscribe_token]))
@section('robots', 'noindex,nofollow')

@section('content')
    @php
        $done = $suppressed || session('outreach_unsubscribed');
        $restored = (bool) session('outreach_restored');
    @endphp

    <div class="journal-shell">
        <header class="journal-utility-header">
            <div class="container-vw">
                <span class="journal-utility-header__label">{{ $studio }} · Email preferences</span>
                @if ($restored)
                    <h1>Your opt-out has been withdrawn.</h1>
                    <p>You will stay on the list, and we will only write when we have something genuinely relevant to say.</p>
                @elseif ($done)
                    <h1>You are unsubscribed.</h1>
                    <p>Your address has been removed and no further email will be sent to it. Nothing else is required from you.</p>
                @else
                    <h1>Take your address off our list.</h1>
                    <p>One click removes you for good. We will not ask why, and we will not write again.</p>
                @endif
            </div>
        </header>

        <section class="journal-follow-guide">
            <div class="container-vw">
                <div class="journal-follow__layout">
                    <div class="journal-follow__aside">
                        <h2>What happens next</h2>
                        @if ($restored)
                            <p>Your address is back on the list. If this was not what you intended, you can remove it again from any email we send.</p>
                        @elseif ($done)
                            <p>The address is on a permanent opt-out list. It stays there even if your details appear on a list we work from later.</p>
                        @else
                            <p>Your address goes on a permanent opt-out list. It stays there even if your details turn up on a list we work from in future.</p>
                        @endif
                        <a href="{{ route('home') }}">Back to the homepage</a>
                    </div>

                    <div class="journal-follow__card" aria-labelledby="unsubscribe-title">
                        <div class="journal-follow__card-top">
                            <span>{{ $prospect->email }}</span>
                            <span>Opt-out</span>
                        </div>

                        @if ($restored)
                            <h2 id="unsubscribe-title">You are still subscribed.</h2>
                            <p>Nothing further to do. You can close this page.</p>
                            <div class="journal-follow__actions">
                                <form method="POST" action="{{ route('outreach.unsubscribe.confirm', ['token' => $prospect->unsubscribe_token]) }}">
                                    @csrf
                                    <button type="submit">Remove me again <span aria-hidden="true">↗</span></button>
                                </form>
                            </div>
                        @elseif ($done)
                            <h2 id="unsubscribe-title">You are on the opt-out list.</h2>
                            <p>
                                @if ($prospect->company)
                                    We have recorded that {{ $prospect->company }} does not want to hear from us.
                                @else
                                    We have recorded that this address does not want to hear from us.
                                @endif
                                If you clicked by mistake, you can put it back.
                            </p>
                            <div class="journal-follow__actions">
                                <form method="POST" action="{{ route('outreach.unsubscribe.undo', ['token' => $prospect->unsubscribe_token]) }}">
                                    @csrf
                                    <button type="submit">This was a mistake — undo <span aria-hidden="true">↗</span></button>
                                </form>
                            </div>
                        @else
                            <h2 id="unsubscribe-title">Confirm you want to opt out</h2>
                            <p>
                                We ask before acting so that a link-scanner checking this email on your behalf cannot unsubscribe you by accident.
                            </p>
                            <div class="journal-follow__actions">
                                <form method="POST" action="{{ route('outreach.unsubscribe.confirm', ['token' => $prospect->unsubscribe_token]) }}">
                                    @csrf
                                    <button type="submit">Yes, remove me <span aria-hidden="true">↗</span></button>
                                </form>
                                <a href="{{ route('home') }}">Stay subscribed <span aria-hidden="true">↗</span></a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
