<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_suppressions', function (Blueprint $table): void {
            $table->id();

            // Kept as an address rather than a foreign key on purpose: the list
            // has to hold people who asked not to be contacted before they were
            // ever imported as prospects, and it must survive a prospect being
            // deleted. This table is the last word on whether an address may be
            // emailed.
            $table->string('email', 180)->unique();

            // unsubscribed | bounced | complaint | asked | manual
            $table->string('reason', 32);

            $table->string('source', 60)->nullable();
            $table->string('note', 200)->nullable();
            $table->timestamp('suppressed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_suppressions');
    }
};
