<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\OutreachProspect;
use Illuminate\Support\Str;

/**
 * Everything worth checking before an outreach message is sent.
 *
 * Two kinds of problem get caught here, and both are the ones that actually
 * cost money. The first is copy that reads like a mailshot: filters and
 * recipients both punish it, and the sender will not notice by reading their own
 * words back. The second is configuration that is missing — no credentials, no
 * postal address — where the panel would otherwise be happy to send into a void.
 *
 * This reports; it never blocks. An administrator who has read a warning may
 * still have a reason, and a tool that refuses to send is worse than one that
 * says what it thinks.
 */
final class OutreachLint
{
    public const LEVEL_WARNING = 'warning';

    public const LEVEL_NOTICE = 'notice';

    /**
     * Phrases that are heavily associated with unsolicited mail. Matched
     * case-insensitively as whole phrases, so ordinary words such as "free" in
     * a sentence stay untouched.
     */
    private const SPAM_PHRASES = [
        'act now',
        'apply now',
        'buy now',
        'cash bonus',
        'click here',
        'congratulations you',
        'dear sir/madam',
        'dear sir or madam',
        'double your',
        'earn extra cash',
        'guaranteed income',
        'limited time offer',
        'make money fast',
        'no obligation',
        'risk free',
        'risk-free',
        'special promotion',
        'this is not spam',
        'urgent response',
        'while supplies last',
        'work from home',
        '$$$',
    ];

    /**
     * @return array<int, array{level: string, message: string}>
     */
    public static function check(string $subject, string $body, ?int $position = null): array
    {
        $issues = [];
        $subject = trim($subject);
        $body = trim($body);

        // A first email that claims to be a reply is a lie the recipient can
        // see through, and it breaks threading in a way that looks deliberate.
        if ($position === 1 && Str::startsWith(mb_strtolower($subject), 're:')) {
            $issues[] = self::warning('The first email starts with "Re:", which reads as a reply to something they never received. Use "Re:" from a follow-up step onward.');
        }

        if (mb_strlen($subject) > 70) {
            $issues[] = self::notice('The subject is '.mb_strlen($subject).' characters; phone inboxes cut it at about 40.');
        }

        if (self::capsRatio($subject) > 0.4 && mb_strlen($subject) > 12) {
            $issues[] = self::warning('The subject is mostly capitals, which filters treat as shouting.');
        }

        $haystack = mb_strtolower($subject.' '.$body);

        foreach (self::SPAM_PHRASES as $phrase) {
            if (str_contains($haystack, $phrase)) {
                $issues[] = self::warning("“{$phrase}” is a phrase spam filters look for. Say the same thing in your own words.");
            }
        }

        $links = self::linkCount($body);

        if ($links > 2) {
            $issues[] = self::notice("The message contains {$links} links. One is normal in a personal note; three or more looks like marketing.");
        }

        if (! self::containsToken($subject.$body)) {
            $issues[] = self::warning('Nothing in this email is personalised. Add {first_name} or {company} so it does not read as a bulk send.');
        }

        $exclamations = substr_count($subject.$body, '!');

        if ($exclamations > 1) {
            $issues[] = self::notice("The message uses {$exclamations} exclamation marks.");
        }

        if (mb_strlen($body) < 200) {
            $issues[] = self::notice('The body is very short — under 200 characters. A cold email needs a reason to exist before it needs to be brief.');
        }

        if (mb_strlen($body) > 1800) {
            $issues[] = self::notice('The body is over 1800 characters. Replies to a first contact usually come from messages of a few paragraphs.');
        }

        // Only the openers need a question. A follow-up that closes a loop
        // ("I will stop here") has nothing to ask, and nagging about it would
        // teach the sender to ignore this check — which is how a useful warning
        // becomes noise. Position null means the caller cannot tell, as inside a
        // repeater item, so the rule stays quiet rather than firing wrongly.
        if ($position === 1 && ! Str::contains($body, '?')) {
            $issues[] = self::notice('There is no question in the message, so there is nothing for the recipient to answer.');
        }

        return $issues;
    }

    /**
     * Problems that are not about the copy: configuration that would stop a
     * send from reaching anyone.
     *
     * @return array<int, string>
     */
    public static function configurationProblems(): array
    {
        $problems = [];

        if (! MailDelivery::isLive()) {
            $problems[] = MailDelivery::problem().' Nothing sent from this section will reach a person until that is fixed.';
        }

        if (blank(config('outreach.footer.postal_address'))) {
            $problems[] = 'No postal address is set (OUTREACH_POSTAL_ADDRESS). A physical address is required by CAN-SPAM and expected under GDPR, and its absence is a common reason cold email is filtered.';
        }

        if (blank(config('mail.reply_to.address')) || config('mail.reply_to.address') === config('mail.from.address')) {
            $problems[] = 'Reply-to is not set to a mailbox a person reads. Replies are the point of outreach, so point MAIL_REPLY_TO_ADDRESS at a monitored inbox.';
        }

        return $problems;
    }

    public static function hasWarnings(array $issues): bool
    {
        foreach ($issues as $issue) {
            if ($issue['level'] === self::LEVEL_WARNING) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<int, array{level: string, message: string}>  $issues
     */
    public static function summary(array $issues): string
    {
        if ($issues === []) {
            return 'No problems found — the copy and the configuration both look sendable.';
        }

        $warnings = count(array_filter($issues, fn (array $issue): bool => $issue['level'] === self::LEVEL_WARNING));

        return $warnings > 0
            ? $warnings.' to look at, '.(count($issues) - $warnings).' minor.'
            : count($issues).' minor suggestion(s).';
    }

    private static function linkCount(string $text): int
    {
        return preg_match_all('#https?://\S+#i', $text) ?: 0;
    }

    private static function containsToken(string $text): bool
    {
        foreach (OutreachProspect::TOKENS as $token) {
            if (str_contains($text, $token)) {
                return true;
            }
        }

        return false;
    }

    private static function capsRatio(string $text): float
    {
        $letters = preg_replace('/[^a-z]/i', '', $text) ?? '';

        if ($letters === '') {
            return 0.0;
        }

        $upper = preg_replace('/[^A-Z]/', '', $text) ?? '';

        return mb_strlen($upper) / mb_strlen($letters);
    }

    /**
     * @return array{level: string, message: string}
     */
    private static function warning(string $message): array
    {
        return ['level' => self::LEVEL_WARNING, 'message' => $message];
    }

    /**
     * @return array{level: string, message: string}
     */
    private static function notice(string $message): array
    {
        return ['level' => self::LEVEL_NOTICE, 'message' => $message];
    }
}
