{{--
    Reply sent to a lead from the admin panel.

    Email clients do not load the site stylesheet, so everything here is inline
    and laid out with tables — the one structure Outlook, Gmail and Apple Mail
    all agree on. The brand gradient sits on a 4px bar over a solid indigo
    fallback, so a client that ignores gradients still shows the brand colour.

    The message body and the quoted enquiry are escaped by e() before the
    newlines become <br />, so no typed or submitted text can inject markup.
--}}
@php
    $studio = (string) config('mail.reply_to.name') ?: config('app.name');
    $replyTo = (string) config('mail.reply_to.address');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ \Illuminate\Support\Str::limit($subjectLine ?? '', 80) }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f6ff;">
    <div style="display:none;max-height:0;overflow:hidden;opacity:0;">
        A reply from {{ $studio }} about your project enquiry.
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f4f6ff;">
        <tr>
            <td align="center" style="padding:32px 16px;">

                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:620px;background-color:#ffffff;border:1px solid #e6e6fa;border-radius:16px;overflow:hidden;">

                    {{-- Brand hairline --}}
                    <tr>
                        <td style="height:4px;line-height:4px;font-size:0;background-color:#4b0082;background-image:linear-gradient(90deg,#18d2ff 0%,#7b41b3 55%,#c8459b 100%);">&nbsp;</td>
                    </tr>

                    <tr>
                        <td style="padding:28px 32px 0 32px;">
                            <span style="font-family:'Segoe UI',Helvetica,Arial,sans-serif;font-size:19px;font-weight:700;letter-spacing:-0.01em;color:#0d0420;">{{ $studio }}</span>
                            <span style="font-family:'Segoe UI',Helvetica,Arial,sans-serif;font-size:11px;font-weight:600;letter-spacing:0.14em;text-transform:uppercase;color:#7b41b3;padding-left:10px;">Reply</span>
                        </td>
                    </tr>

                    {{-- The message the administrator reviewed before sending --}}
                    <tr>
                        <td style="padding:20px 32px 0 32px;font-family:'Segoe UI',Helvetica,Arial,sans-serif;font-size:15px;line-height:1.65;color:#0d1d2a;">
                            {!! nl2br(e($bodyText)) !!}
                        </td>
                    </tr>

                    {{-- The original enquiry, quoted for context --}}
                    <tr>
                        <td style="padding:28px 32px 0 32px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f7f9ff;border:1px solid #e6e6fa;border-radius:12px;">
                                <tr>
                                    <td style="padding:16px 18px;">
                                        <p style="margin:0 0 8px 0;font-family:'Segoe UI',Helvetica,Arial,sans-serif;font-size:10px;font-weight:600;letter-spacing:0.16em;text-transform:uppercase;color:#708090;">
                                            Your original message
                                        </p>
                                        <p style="margin:0;font-family:'Segoe UI',Helvetica,Arial,sans-serif;font-size:13px;line-height:1.6;color:#4c4451;">
                                            {!! nl2br(e($contactRequest->brief)) !!}
                                        </p>
                                        <p style="margin:12px 0 0 0;font-family:'Segoe UI',Helvetica,Arial,sans-serif;font-size:11px;line-height:1.6;color:#708090;">
                                            {{ $contactRequest->name }}@if ($contactRequest->company)
                                                · {{ $contactRequest->company }}
                                            @endif
                                                · {{ $contactRequest->serviceLabel() }}
                                                · {{ $contactRequest->created_at?->format('F j, Y') }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:26px 32px 30px 32px;">
                            <p style="margin:0;border-top:1px solid #e6e6fa;padding-top:16px;font-family:'Segoe UI',Helvetica,Arial,sans-serif;font-size:12px;line-height:1.65;color:#708090;">
                                Reply to this email and it comes straight back to us at
                                <a href="mailto:{{ $replyTo }}" style="color:#4b0082;text-decoration:none;">{{ $replyTo }}</a>.
                            </p>
                        </td>
                    </tr>

                </table>

                <p style="margin:14px 0 0 0;font-family:'Segoe UI',Helvetica,Arial,sans-serif;font-size:11px;color:#708090;">
                    Sent by {{ $studio }} from the project enquiry on the website.
                </p>

            </td>
        </tr>
    </table>
</body>
</html>
