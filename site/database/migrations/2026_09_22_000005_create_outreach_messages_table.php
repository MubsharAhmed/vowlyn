<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outreach_messages', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('outreach_enrollment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('outreach_prospect_id')->constrained()->cascadeOnDelete();
            $table->foreignId('outreach_campaign_id')->constrained()->cascadeOnDelete();

            // Nullable so deleting a step from a sequence does not erase the
            // record of what was already sent to somebody.
            $table->foreignId('outreach_campaign_step_id')->nullable()
                ->constrained('outreach_campaign_steps')->nullOnDelete();

            $table->unsignedSmallInteger('step_position')->nullable();

            $table->string('to_email', 180);
            $table->string('subject', 180);

            // Stored exactly as sent, after token substitution, so the record
            // answers "what did they actually receive?" without guessing.
            $table->text('body');

            // Which mailer handled it — "log" means recorded, not delivered.
            $table->string('transport', 32);
            $table->string('status', 16);
            $table->string('error', 500)->nullable();

            // Why a send was refused before it reached the mailer: suppressed,
            // cooling down, daily limit reached. Recorded rather than skipped
            // silently, because "why did this person never get an email?" is a
            // question that otherwise has no answer.
            $table->string('skip_reason', 200)->nullable();

            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['outreach_prospect_id', 'created_at']);
            $table->index(['status', 'sent_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outreach_messages');
    }
};
