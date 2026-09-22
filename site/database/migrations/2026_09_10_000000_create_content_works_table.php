<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_works', function (Blueprint $table) {
            $table->id();
            $table->string('discipline', 24);
            $table->string('title', 120);
            $table->string('client', 120)->nullable();
            $table->string('description', 500);
            $table->string('image_path')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->string('image_alt', 180)->nullable();
            $table->string('link_url', 500)->nullable();
            $table->unsignedInteger('width')->default(0);
            $table->unsignedInteger('height')->default(0);
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['discipline', 'is_published', 'deleted_at', 'sort_order'], 'content_work_listing');
        });

        $now = now();
        $works = [
            ['photography', 'Course in Motion', 'Caledon Golf Club', 'Campaign imagery shaped around place, pace, and the atmosphere of a day on the course.', 'media/content-creation/caledon-brand-film.jpg', 'Three red golf carts parked beside trees with large “The Golf” lettering over the image', null, 10],
            ['photography', 'Between Shots', 'Caledon Golf Club', 'Detail-led stills that hold the quiet moments between the headline action.', 'media/content-creation/caledon-detail-cut.jpg', 'Golfer in a cap completing a swing on a sunlit tee surrounded by trees', null, 20],
            ['photography', 'Above the Fairway', 'Caledon Golf Club', 'An aerial perspective that establishes scale and gives the campaign room to breathe.', 'media/content-creation/caledon-drone-shot.jpg', 'Aerial view of golfers, fairways, paths, and tall trees at Caledon Golf Club', null, 30],
            ['design', 'Moventra Distribution', 'Global wholesale', 'A confident B2B experience that makes a complex sourcing and distribution journey easier to navigate.', null, null, 'https://www.moventradistribution.com/', 10],
            ['design', 'Meljori Jewellery', 'Fine jewellery', 'A restrained commerce experience designed around product discovery and premium presentation.', null, null, 'https://www.meljorijewellery.ca/', 20],
            ['design', 'IT Bridges', 'Technology services', 'A clear service architecture that turns a broad technical offer into focused user journeys.', null, null, 'https://itbridges.ca/', 30],
        ];

        foreach ($works as [$discipline, $title, $client, $description, $image, $alt, $url, $order]) {
            DB::table('content_works')->insert([
                'discipline' => $discipline, 'title' => $title, 'client' => $client, 'description' => $description,
                'image_path' => $image, 'thumbnail_path' => $image, 'image_alt' => $alt, 'link_url' => $url,
                'sort_order' => $order, 'is_published' => true, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('content_works');
    }
};
