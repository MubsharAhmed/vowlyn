<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ContentVideo;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Process\Process;
use Throwable;

final class ContentVideoService
{
    public function save(ContentVideo $record, array $data): ContentVideo
    {
        try {
            return $this->saveUnderLock($record, $data);
        } catch (LockTimeoutException) {
            $this->invalid('Another video is being processed. Please wait a moment, then save again.');
        }
    }

    private function saveUnderLock(ContentVideo $record, array $data): ContentVideo
    {
        // All writes, including replacements, share the quota lock. Never trust paths from form state.
        return Cache::store('file')->lock('content-video-upload', 180)->block(5, function () use ($record, $data): ContentVideo {
            $rules = [
                'title' => ['required', 'string', 'max:120'], 'client' => ['nullable', 'string', 'max:120'],
                'type' => ['required', 'string', 'max:60'], 'note' => ['required', 'string', 'max:500'],
                'transcript' => ['nullable', 'string', 'max:15000'],
                'sort_order' => ['required', 'integer', 'min:0', 'max:100000'],
                'is_published' => ['required', 'boolean'], 'is_featured' => ['required', 'boolean'],
                'video_upload' => [$record->exists ? 'nullable' : 'required', 'file', 'mimetypes:video/mp4,application/mp4', 'max:'.config('content-videos.max_upload_kb')],
                'poster_upload' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:max_width=4096,max_height=4096'],
            ];
            if (! $record->exists || ! empty($data['video_upload'])) {
                $rules['rights_confirmed'] = ['accepted'];
            }
            Validator::make(['data' => $data], collect($rules)->mapWithKeys(fn ($rule, $key) => ['data.'.$key => $rule])->all())->validate();
            $upload = $data['video_upload'] ?? null;
            $cover = $data['poster_upload'] ?? null;
            $created = [];
            $old = [$record->video_path, $record->poster_path];
            $disk = Storage::disk('public');
            $media = [];

            try {
                if ($upload || $cover) {
                    $reserve = ($upload?->getSize() ?? 0) + 2 * 1024 * 1024;
                    if ($this->storageBytes() + $reserve > config('content-videos.storage_limit_mb') * 1024 * 1024) {
                        $this->invalid('The video library is full. Permanently delete unused uploads from Trash, or ask your developer to increase storage.');
                    }
                    $disk->makeDirectory('content-videos');
                    $free = disk_free_space($disk->path('content-videos'));
                    if ($free !== false && $free < $reserve + 256 * 1024 * 1024) {
                        $this->invalid('The server is low on free space. Please contact your developer before uploading.');
                    }
                }

                if ($upload instanceof UploadedFile) {
                    $media = $this->inspect($upload->getRealPath());
                    $media['video_path'] = 'content-videos/'.Str::uuid().'.mp4';
                    $created[] = $media['video_path'];
                    // Lossless stream copy is quick: no CPU-heavy re-encoding on the web server.
                    $this->run([
                        config('content-videos.ffmpeg'), '-v', 'error', '-nostdin', '-y', '-protocol_whitelist', 'file,pipe',
                        '-i', $upload->getRealPath(), '-map', '0:v:0', '-map', '0:a:0?', '-c', 'copy',
                        '-map_metadata', '-1', '-map_chapters', '-1', '-movflags', '+faststart', $disk->path($media['video_path']),
                    ], 45);
                    $media['size_bytes'] = $disk->size($media['video_path']);
                }

                if ($upload || $cover) {
                    $source = $cover ? $cover->getRealPath() : $disk->path($media['video_path']);
                    $media['poster_path'] = 'content-videos/'.Str::uuid().'.jpg';
                    $created[] = $media['poster_path'];
                    $this->run([
                        config('content-videos.ffmpeg'), '-v', 'error', '-nostdin', '-y', '-threads', '1', '-protocol_whitelist', 'file,pipe',
                        '-i', $source, '-frames:v', '1', '-vf', 'scale=720:1120:force_original_aspect_ratio=decrease', '-q:v', '4',
                        '-map_metadata', '-1', '-threads', '1', $disk->path($media['poster_path']),
                    ], 20);
                    if (! $disk->exists($media['poster_path']) || $disk->size($media['poster_path']) === 0) {
                        $this->invalid('A cover could not be generated. Please try a different MP4 or cover image.');
                    }
                    if ($this->storageBytes() > config('content-videos.storage_limit_mb') * 1024 * 1024) {
                        $this->invalid('This upload would exceed the library storage limit.');
                    }
                }

                DB::transaction(function () use ($record, $data, $media, $old): void {
                    $record->fill(Arr::only($data, ['title', 'client', 'type', 'note', 'transcript', 'sort_order', 'is_published', 'is_featured']));
                    foreach ($media as $key => $value) {
                        $record->setAttribute($key, $value);
                    }
                    $record->save();
                    if ($record->is_featured) {
                        ContentVideo::withTrashed()->whereKeyNot($record->id)->update(['is_featured' => false]);
                    }
                    DB::afterCommit(fn () => $this->deleteUnused($old));
                });

                return $record;
            } catch (Throwable $e) {
                $disk->delete($created);
                if ($e instanceof ValidationException) {
                    throw $e;
                }
                report($e);
                $this->invalid('The video could not be saved. Your previous media has been kept. Try again, or ask your developer to check FFmpeg and server permissions.');
            }
        });
    }

    private function inspect(string $path): array
    {
        $output = $this->run([config('content-videos.ffprobe'), '-v', 'error', '-protocol_whitelist', 'file,pipe', '-show_streams', '-show_format', '-of', 'json', $path], 15);
        $info = json_decode($output, true, flags: JSON_THROW_ON_ERROR);
        $streams = collect($info['streams'] ?? []);
        $video = $streams->firstWhere('codec_type', 'video');
        $audio = $streams->firstWhere('codec_type', 'audio');
        $duration = (float) ($info['format']['duration'] ?? 0);
        $width = (int) ($video['width'] ?? 0);
        $height = (int) ($video['height'] ?? 0);
        $rate = explode('/', $video['avg_frame_rate'] ?? '0/1');
        $fps = (float) $rate[0] / max(1, (float) ($rate[1] ?? 1));
        if (! $video || ($video['codec_name'] ?? '') !== 'h264' || ($video['pix_fmt'] ?? '') !== 'yuv420p' || ($audio && ($audio['codec_name'] ?? '') !== 'aac')) {
            $this->invalid('Export as MP4 with H.264 video (8-bit) and AAC audio. MOV, HEVC/H.265 and ProRes are not supported.');
        }
        if ($duration < 1 || $duration > config('content-videos.max_duration_seconds')) {
            $this->invalid('Choose a video between 1 second and 3 minutes long.');
        }
        if (min($width, $height) < 1 || min($width, $height) > 1080 || max($width, $height) > 1920 || $fps > 60.1) {
            $this->invalid('Export at 1080p or smaller, with a maximum of 60 frames per second. Portrait and landscape are supported.');
        }
        if ((float) ($info['format']['bit_rate'] ?? 0) > 12000000) {
            $this->invalid('This video is too heavy for smooth web playback. Export at a bitrate of 12 Mbps or less.');
        }

        return ['duration_seconds' => (int) ceil($duration), 'width' => $width, 'height' => $height];
    }

    private function run(array $command, int $timeout): string
    {
        $process = new Process($command);
        $process->setTimeout($timeout);
        $process->mustRun();

        return $process->getOutput();
    }

    public function storageBytes(): int
    {
        $disk = Storage::disk('public');

        return (int) collect($disk->allFiles('content-videos'))->sum(fn ($path) => $disk->size($path));
    }

    public function serverWarnings(): string
    {
        $warnings = [];
        foreach (['ffmpeg', 'ffprobe'] as $tool) {
            if (! is_executable(config('content-videos.'.$tool))) {
                $warnings[] = strtoupper($tool).' is missing. Ask your developer to complete the video server setup.';
            }
        }
        if (! app()->runningUnitTests()) {
            foreach (['upload_max_filesize' => 50, 'post_max_size' => 64] as $setting => $minimumMb) {
                $value = ini_get($setting);
                $bytes = ini_parse_quantity($value);
                if ($bytes > 0 && $bytes < $minimumMb * 1048576) {
                    $warnings[] = "Server limit: {$setting} is {$value}; your developer must raise it to {$minimumMb}M for full-size uploads.";
                }
            }
        }

        return implode(' ', $warnings);
    }

    public function deleteUnused(array $paths): void
    {
        try {
            foreach (array_filter($paths) as $path) {
                // Never touch bundled files or other features' uploads, even if a record has an unexpected path.
                if (! preg_match('/\Acontent-videos\/[a-f0-9-]{36}\.(mp4|jpg)\z/', $path)) {
                    continue;
                }
                if (! ContentVideo::withTrashed()->where('video_path', $path)->orWhere('poster_path', $path)->exists()) {
                    Storage::disk('public')->delete($path);
                }
            }
        } catch (Throwable $e) {
            // Cleanup runs after commit. Its failure must never invalidate a successful
            // save or trigger removal of the replacement files. Retained files count toward quota.
            report($e);
        }
    }

    private function invalid(string $message): never
    {
        throw ValidationException::withMessages(['data.video_upload' => $message]);
    }
}
