<?php

return [
    'max_upload_kb' => 8192,
    'storage_limit_mb' => 500,
    'max_pixels' => 40000000,
    'magick' => env('IMAGEMAGICK_BINARY', '/usr/bin/magick'),
];
