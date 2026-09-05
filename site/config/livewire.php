<?php

use App\Http\Middleware\RequireVerifiedUploader;

return [
    'temporary_file_upload' => [
        'disk' => 'local',
        'rules' => ['required', 'file', 'max:51200'],
        'middleware' => [RequireVerifiedUploader::class, 'throttle:20,1'],
        'max_upload_time' => 10,
        'cleanup' => true,
    ],
];
