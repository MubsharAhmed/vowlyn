<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outreach_enrollments', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('outreach_campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('outreach_prospect_id')->constrained()->cascadeOnDelete();

            // The 1-based position of the next step to send. Kept as a position
            // rather than a foreign key so reordering or deleting a step in the
            // sequence never moves somebody to a different message.
            $table->unsignedSmallInteger('current_step')->default(1);

            // active | replied | interested | not_interested | unsubscribed |
            // bounced | finished | paused. Anything other than active stops the
            // sequence for this person.
            $table->string('status', 16)->default('active');

            $table->string('stop_reason', 200)->nullable();
            $table->timestamp('next_send_at')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->timestamp('enrolled_at')->nullable();
            $table->timestamps();

            // One person can be in several campaigns, but never twice in the
            // same one.
            $table->unique(['outreach_campaign_id', 'outreach_prospect_id'], 'outreach_enrollments_campaign_prospect_unique');
            $table->index(['status', 'next_send_at']);
        });

        $now = now();

        $campaignId = (int) DB::table('outreach_campaigns')->value('id');

        if ($campaignId === 0) {
            return;
        }

        /*
        | A handful of example prospects.
        |
        | Every address is on example.com, which is reserved by RFC 2606 and
        | cannot receive mail. That is deliberate: the demo data is here so the
        | panel has something real to show, and it stays safe even if somebody
        | activates the starter campaign by mistake — the messages bounce
        | instead of reaching strangers.
        */
        $prospects = [
            ['company' => 'Halstead & Rowe', 'contact_name' => 'Priya Halstead', 'email' => 'priya@halstead-rowe.example.com', 'role' => 'Director', 'industry' => 'Accountancy', 'region' => 'Manchester', 'website' => 'halstead-rowe.example.com', 'source' => 'Local directory', 'status' => 'new'],
            ['company' => 'Northgate Dental', 'contact_name' => 'Tom Brightwell', 'email' => 'tom@northgate-dental.example.com', 'role' => 'Practice Manager', 'industry' => 'Dental care', 'region' => 'Leeds', 'website' => 'northgate-dental.example.com', 'source' => 'Google Maps', 'status' => 'new'],
            ['company' => 'Copper Lane Interiors', 'contact_name' => 'Ana Duarte', 'email' => 'ana@copperlane.example.com', 'role' => 'Founder', 'industry' => 'Interior design', 'region' => 'Bristol', 'website' => 'copperlane.example.com', 'source' => 'Referral', 'status' => 'new'],
            ['company' => 'Rowan Fitness', 'contact_name' => 'Dean Rowan', 'email' => 'dean@rowanfitness.example.com', 'role' => 'Owner', 'industry' => 'Fitness', 'region' => 'Sheffield', 'website' => 'rowanfitness.example.com', 'source' => 'Trade show', 'status' => 'new'],
            ['company' => 'Beacon Legal', 'contact_name' => 'Sarah Ochieng', 'email' => 'sarah@beaconlegal.example.com', 'role' => 'Partner', 'industry' => 'Legal services', 'region' => 'Birmingham', 'website' => 'beaconlegal.example.com', 'source' => 'Local directory', 'status' => 'not_interested', 'notes' => 'Asked to be left alone in March — kept as a record, not to be contacted again.'],
            ['company' => 'Fernhill Joinery', 'contact_name' => 'Ollie Fern', 'email' => 'ollie@fernhilljoinery.example.com', 'role' => 'Workshop Lead', 'industry' => 'Construction', 'region' => 'York', 'website' => 'fernhilljoinery.example.com', 'source' => 'Google Maps', 'status' => 'unsubscribed', 'notes' => 'Unsubscribed from the last sequence.'],
        ];

        $ids = [];

        foreach ($prospects as $prospect) {
            $ids[$prospect['email']] = DB::table('outreach_prospects')->insertGetId($prospect + [
                'unsubscribe_token' => Str::random(40),
                'last_contacted_at' => in_array($prospect['status'], ['new'], true) ? null : $now->copy()->subDays(20),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Enroll the ones who are eligible, in the draft campaign. A draft
        // campaign never sends, so these are waiting rather than in flight.
        foreach (['priya@halstead-rowe.example.com', 'tom@northgate-dental.example.com', 'ana@copperlane.example.com'] as $email) {
            DB::table('outreach_enrollments')->insert([
                'outreach_campaign_id' => $campaignId,
                'outreach_prospect_id' => $ids[$email],
                'current_step' => 1,
                'status' => 'active',
                'next_send_at' => $now,
                'enrolled_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        DB::table('outreach_enrollments')->insert([
            'outreach_campaign_id' => $campaignId,
            'outreach_prospect_id' => $ids['ollie@fernhilljoinery.example.com'],
            'current_step' => 1,
            'status' => 'unsubscribed',
            'stop_reason' => 'Unsubscribed before the first message was sent.',
            'next_send_at' => null,
            'enrolled_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('outreach_enrollments');
    }
};
