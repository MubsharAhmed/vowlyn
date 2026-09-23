{{--
    Outreach email — first contact with somebody who has never written to us.

    Kept deliberately plain. Most cold email is discarded in the second the
    recipient decides it is a mailshot, and a branded template with a header
    image is the fastest way to that decision. So: system fonts, one column,
    no images, no tracking pixel, no styled button. The only link above the
    footer is one the sender chose to write.

    The body is escaped before newlines become <br />, so neither typed copy nor
    imported prospect details can inject markup into the message.
--}}
@php
    $studio = $studio ?? config('mail.reply_to.name');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ \Illuminate\Support\Str::limit($subjectLine ?? '', 80) }}</title>
</head>
<body style="margin:0;padding:0;background-color:#ffffff;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td align="left" style="padding:28px 20px 0 20px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:600px;">

                    {{-- The message, as the sender wrote it --}}
                    <tr>
                        <td style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;font-size:15px;line-height:1.62;color:#20222b;">
                            {!! nl2br(e($bodyText)) !!}
                        </td>
                    </tr>

                    {{-- Opt-out and identity. Required by CAN-SPAM, expected under
                         GDPR, and the first thing a cautious reader looks for. --}}
                    <tr>
                        <td style="padding:26px 0 30px 0;">
                            <p style="margin:0;border-top:1px solid #e6e8ef;padding-top:14px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;font-size:12px;line-height:1.6;color:#8a90a0;">
                                @if ($goal)
                                    You are receiving this because {{ $prospect->company ?: 'your business' }} looked like a good fit for {{ $goal }}.
                                @else
                                    You are receiving this because we thought it might be relevant to {{ $prospect->company ?: 'your business' }}.
                                @endif
                                If it is not, <a href="{{ $unsubscribeUrl }}" style="color:#5b6072;text-decoration:underline;">unsubscribe in one click</a> and we will not write again.
                            </p>
                            <p style="margin:10px 0 0 0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;font-size:12px;line-height:1.6;color:#8a90a0;">
                                {{ $studio }}@if ($postalAddress) · {{ $postalAddress }}@endif
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
