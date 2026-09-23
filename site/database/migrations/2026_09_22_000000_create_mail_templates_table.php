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
        Schema::create('mail_templates', function (Blueprint $table): void {
            $table->id();
            $table->string('service', 64)->nullable();
            $table->string('name', 120);
            $table->string('subject', 180);
            $table->text('body');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Several replies can target the same service; the picker groups them.
            $table->index(['service', 'is_active']);
        });

        $now = now();
        $templates = [
            [
                'service' => null,
                'name' => 'General reply',
                'subject' => 'Thanks for reaching out — {studio}',
                'body' => <<<'TXT'
Hi {name},

Thanks for getting in touch about {service}, and for the detail you shared about {company}. It is always easier to respond well to a brief that has been thought through.

I have read through what you sent and I would like to ask two things before I give you a considered answer:

1. What is driving the timing — is there a date or an event this needs to be ready for?
2. Who else needs to be comfortable with the plan before it goes ahead?

If it is quicker to talk it through, you can book a 30-minute call here: https://calendly.com/junaidswati/new-meeting

Either way, you will hear from a person who would actually do the work — not a representative reading a script.

Best,
{studio}
TXT,
            ],
            [
                'service' => 'web-app-development',
                'name' => 'Web app development reply',
                'subject' => 'Your web project — next steps',
                'body' => <<<'TXT'
Hi {name},

Thanks for the detail on {company} — it is clear you know what you want this to do.

Most web projects of this shape start with a short blueprint sprint: we map the user journeys, the data model and the integrations, then come back with a fixed scope, a timeline and a price. That way you are committing to a plan you can read, not an open-ended estimate.

Two things that would help me scope this properly:

1. Which parts already exist — a design, a database, or an older site we would be replacing?
2. What has to work on day one versus what can follow a few weeks later?

If it is easier to talk it through, you can book a 30-minute call here: https://calendly.com/junaidswati/new-meeting

Best,
{studio}
TXT,
            ],
            [
                'service' => 'mobile-app-development',
                'name' => 'Mobile app development reply',
                'subject' => 'Your mobile app — a few questions',
                'body' => <<<'TXT'
Hi {name},

Thanks for writing in about {company}. Mobile projects live or die on the details, so I would rather ask a few questions than guess.

Two that shape the whole build:

1. Does this need to be on both the App Store and Google Play, or is one enough to start?
2. Is there an existing backend the app should talk to, or would we be building that too?

For most products one React Native codebase covers both stores without the app feeling like a compromise. Where heavy graphics or deep platform APIs are involved, we will tell you honestly that native is the better call — and why.

Happy to talk it through if that is easier: https://calendly.com/junaidswati/new-meeting

Best,
{studio}
TXT,
            ],
            [
                'service' => 'ai-development',
                'name' => 'AI engineering reply',
                'subject' => 'Bringing AI into {company} — first steps',
                'body' => <<<'TXT'
Hi {name},

Thanks for getting in touch about AI for {company}. The most useful thing I can do at this stage is separate what will genuinely help from what is currently a demo.

Two questions that decide the approach:

1. What data would the feature need to work from, and where does that data live today?
2. How will we judge whether it is working — what does "good enough" look like in your terms?

We build these the way we build any other system: an evaluation set to measure quality, guardrails on the output, fallbacks when a model fails, and visibility into cost. If AI is not the right tool for the problem, I will say so rather than sell you a prototype.

You can book a 30-minute call here: https://calendly.com/junaidswati/new-meeting

Best,
{studio}
TXT,
            ],
            [
                'service' => 'saas-development',
                'name' => 'SaaS platform reply',
                'subject' => 'Your SaaS platform — scoping the first release',
                'body' => <<<'TXT'
Hi {name},

Thanks for the detail about {company}. Building a platform is as much a commercial decision as a technical one, so I want to understand both.

Two things I would like to pin down:

1. What is the core loop a customer pays for — the one thing that has to work before anything else?
2. How will you charge: per seat, per usage, or a flat plan? Billing shapes the data model early.

We usually ship the core loop first with real users, then layer billing, roles and admin tooling on top of a foundation that was chosen to scale — so the first release does not have to be thrown away later.

Happy to talk it through: https://calendly.com/junaidswati/new-meeting

Best,
{studio}
TXT,
            ],
            [
                'service' => 'enterprise-security',
                'name' => 'Enterprise security reply',
                'subject' => 'Security review for {company} — how we run it',
                'body' => <<<'TXT'
Hi {name},

Thanks for reaching out about security for {company}. Security work is usually blocking something specific, so I would like to know what is behind the request.

Two questions to start:

1. Is there a deal, questionnaire or audit date this is tied to?
2. Which areas concern you most — authentication, access control, data handling, or the pipeline itself?

Typically we assess first, then hand back a prioritised, plain-English remediation plan with the highest-risk items at the top. We implement the fixes as well, because a report nobody acts on is not worth much.

You can book a 30-minute call here: https://calendly.com/junaidswati/new-meeting

Best,
{studio}
TXT,
            ],
            [
                'service' => 'cloud-devops',
                'name' => 'Cloud & DevOps reply',
                'subject' => 'Infrastructure and deploys — where to start',
                'body' => <<<'TXT'
Hi {name},

Thanks for the note about {company}. Infrastructure problems are usually felt as friction rather than seen as a project, so help me locate the friction.

Two questions:

1. What does a deploy look like today — how often do you ship, and how confident are you when you do?
2. Is cost, reliability or delivery speed the bigger pain right now?

Those three pull in different directions, and the order we fix them in matters. Most teams get the most relief from making deploys boring first: automated builds, tests on every merge, and a rollback that actually works.

Happy to talk it through: https://calendly.com/junaidswati/new-meeting

Best,
{studio}
TXT,
            ],
            [
                'service' => 'content-creation',
                'name' => 'Content creation reply',
                'subject' => 'Your content project — planning the shoot',
                'body' => <<<'TXT'
Hi {name},

Thanks for thinking of us for {company} — the brief gives me a good sense of the story you want to tell.

Content work goes smoothly when the plan is settled before anyone is on set, so two questions:

1. Where will this be used first — a campaign, a website, social, or all three?
2. Is there a date or launch this has to be ready for?

We plan one clear idea and then shoot for the channels it has to travel across, so a single production day yields a connected library of assets rather than one film and a pile of leftovers.

Happy to talk it through: https://calendly.com/junaidswati/new-meeting

Best,
{studio}
TXT,
            ],
            [
                'service' => 'performance-marketing',
                'name' => 'Marketing reply',
                'subject' => 'Marketing for {company} — measuring what matters',
                'body' => <<<'TXT'
Hi {name},

Thanks for getting in touch about marketing for {company}. I would rather start from the numbers than from the tactics.

Two questions:

1. What counts as a good month for you — leads, revenue, or qualified conversations?
2. What has already been tried, and what did it produce?

That second question usually saves the most money. We would rather improve a channel that is nearly working than start a new one that is fashionable, and we set up measurement first so you can see whether spend is earning its keep.

Happy to talk it through: https://calendly.com/junaidswati/new-meeting

Best,
{studio}
TXT,
            ],
        ];

        foreach ($templates as $template) {
            DB::table('mail_templates')->insert($template + [
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('mail_templates');
    }
};
