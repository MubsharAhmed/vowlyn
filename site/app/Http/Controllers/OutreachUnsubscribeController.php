<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\EmailSuppression;
use App\Models\OutreachEnrollment;
use App\Models\OutreachProspect;
use App\Services\OutreachDispatcher;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * The opt-out a recipient reaches from an outreach email.
 *
 * The GET route deliberately does not unsubscribe anybody. Corporate mail
 * scanners (Outlook Safe Links, Proofpoint, Barracuda and friends) follow links
 * in incoming mail before the recipient ever sees the message, so an
 * unsubscribe that fires on GET quietly removes people who never clicked
 * anything — which is exactly the kind of bug that is invisible until a campaign
 * underperforms. GET asks; POST acts.
 *
 * POST is also what RFC 8058 one-click unsubscribe uses, which is why it is
 * exempt from CSRF: a mail provider posting on the recipient's behalf has no
 * session and no token. The token in the URL is the credential.
 */
final class OutreachUnsubscribeController extends Controller
{
    public function __construct(private readonly OutreachDispatcher $dispatcher) {}

    public function show(string $token): View
    {
        $prospect = $this->prospect($token);

        return view('pages.outreach.unsubscribe', [
            'prospect' => $prospect,
            'suppressed' => $prospect->isSuppressed(),
            'studio' => (string) config('mail.reply_to.name'),
        ]);
    }

    /**
     * Honour the opt-out.
     *
     * A browser posting the form is sent back to the confirmation page; a mail
     * client performing a one-click unsubscribe just needs a 200 and no body it
     * will try to render.
     */
    public function confirm(Request $request, string $token): RedirectResponse|Response
    {
        $prospect = $this->prospect($token);

        $this->dispatcher->unsubscribe(
            $prospect,
            EmailSuppression::REASON_UNSUBSCRIBED,
            'Unsubscribed from an outreach email.',
        );

        if (! str_contains((string) $request->header('Accept', ''), 'text/html')) {
            return response('You have been unsubscribed. No further email will be sent.', 200)
                ->header('Content-Type', 'text/plain; charset=UTF-8');
        }

        return redirect()
            ->route('outreach.unsubscribe', ['token' => $token])
            ->with('outreach_unsubscribed', true);
    }

    /**
     * Reversing an opt-out, for the case that matters: somebody clicked by
     * mistake, or a shared mailbox dealt with it on someone else's behalf.
     * A suppression added by a bounce or a complaint is not reversible here,
     * because the address is the problem, not the preference.
     */
    public function undo(string $token): RedirectResponse
    {
        $prospect = $this->prospect($token);

        $suppression = EmailSuppression::query()->where('email', $prospect->email)->first();

        if ($suppression !== null && $suppression->reason === EmailSuppression::REASON_UNSUBSCRIBED) {
            $suppression->delete();

            if ($prospect->status === OutreachProspect::STATUS_UNSUBSCRIBED) {
                $prospect->update(['status' => OutreachProspect::STATUS_CONTACTED]);
            }

            // The sequences that were ended stay ended. Restoring an opt-out is
            // not the same as asking to be written to again, and quietly
            // re-adding somebody to a live sequence would be the wrong reading
            // of an ambiguous click.
            $prospect->enrollments()
                ->where('status', OutreachEnrollment::STATUS_UNSUBSCRIBED)
                ->update(['stop_reason' => 'Opt-out withdrawn; sequence not restarted.']);
        }

        return redirect()
            ->route('outreach.unsubscribe', ['token' => $token])
            ->with('outreach_restored', true);
    }

    private function prospect(string $token): OutreachProspect
    {
        return OutreachProspect::query()->where('unsubscribe_token', $token)->firstOrFail();
    }
}
