<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outreach_prospects', function (Blueprint $table): void {
            $table->id();

            $table->string('company', 150)->nullable();
            $table->string('contact_name', 120)->nullable();

            // Unique because emailing the same person twice from two lists is
            // the fastest way to be reported as spam.
            $table->string('email', 180)->unique();

            $table->string('role', 120)->nullable();
            $table->string('industry', 120)->nullable();
            $table->string('region', 120)->nullable();
            $table->string('website', 200)->nullable();
            $table->string('linkedin_url', 200)->nullable();

            // Where this person came from — a directory, an event, a referral.
            // Kept because "can you tell me where you got my address?" is a
            // question that gets asked, and a blank answer is a bad one.
            $table->string('source', 60)->nullable();

            $table->string('status', 24)->default('new');
            $table->text('notes')->nullable();

            // Opaque public identifier for the unsubscribe link. A random token
            // rather than a signed URL so it cannot be invalidated later by
            // rotating APP_KEY — an unsubscribe link that stops working is a
            // compliance problem, not a cosmetic one.
            $table->string('unsubscribe_token', 40)->unique();

            $table->timestamp('last_contacted_at')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'last_contacted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outreach_prospects');
    }
};
