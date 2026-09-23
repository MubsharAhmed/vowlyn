<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outreach_campaigns', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 150);

            // The offer, in one line. Shown in the panel and used in the email
            // footer ("we thought {company} might be interested in ..."), so it
            // doubles as the honest reason the recipient is hearing from us.
            $table->string('goal', 200)->nullable();

            // draft | active | paused | finished. Only `active` campaigns send.
            $table->string('status', 16)->default('draft');

            // Optional per-campaign ceiling; the global daily limit still wins.
            $table->unsignedSmallInteger('daily_limit')->nullable();

            $table->text('notes')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('paused_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });

        Schema::create('outreach_campaign_steps', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('outreach_campaign_id')->constrained()->cascadeOnDelete();

            // 1-based order within the sequence.
            $table->unsignedSmallInteger('position');

            // Days to wait after the previous step was sent. For the first step
            // this is the delay after somebody is enrolled, which is almost
            // always 0.
            $table->unsignedSmallInteger('delay_days')->default(0);

            $table->string('subject', 180);
            $table->text('body');
            $table->timestamps();

            $table->unique(['outreach_campaign_id', 'position']);
        });

        $now = now();

        $campaignId = DB::table('outreach_campaigns')->insertGetId([
            'name' => 'Website audit — independent businesses',
            'goal' => 'a free 10-minute website audit',
            'status' => 'draft',
            'daily_limit' => null,
            'notes' => 'Starter sequence to edit, not a campaign to send as-is. It is left in draft, and every example prospect uses an example.com address that cannot receive mail, so nothing can go out by accident.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $steps = [
            [
                'position' => 1,
                'delay_days' => 0,
                'subject' => 'Quick note about the {company} website',
                'body' => <<<'TXT'
Hi {first_name},

I had a look at {company} while researching businesses around {region}, and noticed the site does not show up for the searches that would bring you the most work — the ones where somebody nearby is already looking for what you do.

I run a small studio that fixes this for businesses like yours. No pitch attached: I will record a 10-minute walkthrough of your site with the three changes I would make first, and send it over. If it is useful, we talk. If not, you keep the notes.

Would that be worth ten minutes?

{studio}
TXT,
            ],
            [
                'position' => 2,
                'delay_days' => 4,
                'subject' => 'Re: Quick note about the {company} website',
                'body' => <<<'TXT'
Hi {first_name},

Following up once, in case the first note landed at a busy moment.

One thing I did not mention: the audit is genuinely free, and the most common finding for a {industry} business is not design — it is that the site takes five seconds to load on a phone, which quietly loses enquiries that never arrive.

Happy to send it over either way. If now is not the time, just say so and I will leave you alone.

{studio}
TXT,
            ],
            [
                'position' => 3,
                'delay_days' => 10,
                'subject' => 'Closing the loop, {first_name}',
                'body' => <<<'TXT'
Hi {first_name},

I will stop here — you have heard from me twice and I am not going to keep filling your inbox.

If the timing changes, or the website becomes a priority later in the year, reply to this email and it comes straight to me. The offer of the free audit stands.

All the best with {company}.

{studio}
TXT,
            ],
        ];

        foreach ($steps as $step) {
            DB::table('outreach_campaign_steps')->insert($step + [
                'outreach_campaign_id' => $campaignId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('outreach_campaign_steps');
        Schema::dropIfExists('outreach_campaigns');
    }
};
