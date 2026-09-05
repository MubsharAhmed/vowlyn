<?php

return [
    'max_upload_kb' => 51200,
    'max_duration_seconds' => 180,
    'storage_limit_mb' => (int) env('CONTENT_VIDEO_STORAGE_MB', 2048),
    'ffmpeg' => env('FFMPEG_BINARY', '/usr/bin/ffmpeg'),
    'ffprobe' => env('FFPROBE_BINARY', '/usr/bin/ffprobe'),
];
