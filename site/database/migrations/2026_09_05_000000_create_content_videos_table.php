<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_videos', function (Blueprint $table) {
            $table->id();
            $table->string('title', 120);
            $table->string('client', 120)->nullable();
            $table->string('type', 60);
            $table->string('note', 500);
            $table->text('transcript')->nullable();
            $table->string('video_path');
            $table->string('poster_path');
            $table->unsignedInteger('duration_seconds');
            $table->unsignedInteger('width')->default(0);
            $table->unsignedInteger('height')->default(0);
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['is_published', 'deleted_at', 'sort_order']);
        });

        // Preserve the existing collection. These bundled assets are never deleted by the admin.
        $films = [
            ['The Full Round', 'Brand film', 50, 'caledon-brand-film', 'A longer-form vertical story built to establish place, pace, and atmosphere.'],
            ['Course Notes', 'Social reel', 14, 'caledon-course-reel', 'A concise course edit designed for mobile-first discovery and repeat viewing.'],
            ['Between Shots', 'Detail cut', 9, 'caledon-detail-cut', 'A textural micro-story focused on the details that make the experience memorable.'],
            ['Weekend Loop', 'Short-form edit', 9, 'caledon-social-cut', 'A compact, rhythmic cut made for Reels, Shorts, and campaign variations.'],
            ['Above the Fairway', 'Drone sequence', 9, 'caledon-drone-shot', 'An aerial perspective that gives the campaign scale and a strong visual reset.'],
        ];
        foreach ($films as $index => [$title, $type, $duration, $file, $note]) {
            DB::table('content_videos')->insert([
                'title' => $title, 'client' => 'Caledon Golf Club', 'type' => $type, 'note' => $note,
                'duration_seconds' => $duration, 'video_path' => 'media/content-creation/'.$file.'.mp4',
                'poster_path' => 'media/content-creation/'.$file.'.jpg', 'sort_order' => ($index + 1) * 10,
                'is_published' => true, 'is_featured' => $index === 0, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('content_videos');
    }
};
