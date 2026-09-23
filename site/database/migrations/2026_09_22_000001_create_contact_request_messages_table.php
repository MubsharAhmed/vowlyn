<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_request_messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('contact_request_id')->constrained()->cascadeOnDelete();
            // A template can be renamed or removed without losing the reply history.
            $table->foreignId('mail_template_id')->nullable()->constrained('mail_templates')->nullOnDelete();
            $table->string('to_email', 180);
            $table->string('to_name', 120)->nullable();
            $table->string('subject', 180);
            $table->text('body');
            // Which mailer handled it — "log" means it was recorded, not delivered.
            $table->string('transport', 32);
            $table->string('status', 16);
            $table->string('error', 500)->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['contact_request_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_request_messages');
    }
};
