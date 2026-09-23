<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\OutreachCampaign;
use App\Services\OutreachDispatcher;
use App\Support\MailDelivery;
use Illuminate\Console\Command;

/**
 * The scheduled sender.
 *
 * Sending is driven by this command rather than by queued jobs so that the one
 * thing outreach depends on is a cron entry, not a worker process somebody has
 * to keep alive. When nothing is configured this command says so out loud
 * instead of appearing to work, because a silent scheduled task is worse than
 * no scheduled task: it looks like the campaign is running.
 */
final class SendOutreach extends Command
{
    protected $signature = 'outreach:send
                            {--limit= : How many messages this run may send at most}
                            {--campaign= : Only send for this campaign id}';

    protected $description = 'Send any outreach messages that are due, inside the sending window and daily limit';

    public function handle(OutreachDispatcher $dispatcher): int
    {
        $campaign = null;

        if ($this->option('campaign') !== null) {
            $campaign = OutreachCampaign::query()->find($this->option('campaign'));

            if ($campaign === null) {
                $this->components->error('No campaign with that id.');

                return self::FAILURE;
            }
        }

        if (! MailDelivery::isLive()) {
            $this->components->warn(
                'Scheduled outreach is paused because email is not configured ('.MailDelivery::transport().'). No sequence was advanced. See MAIL-REPLIES.md.',
            );

            return self::FAILURE;
        }

        $limit = $this->option('limit') !== null ? (int) $this->option('limit') : null;

        $result = $dispatcher->run($limit, $campaign);

        if ($result['sent'] > 0 || $result['failed'] > 0) {
            $this->components->info($result['message']);
        } else {
            $this->components->line($result['message']);
        }

        if ($result['failed'] > 0) {
            $this->components->warn('Some sends failed. The panel records the reason on each message.');
        }

        return self::SUCCESS;
    }
}
