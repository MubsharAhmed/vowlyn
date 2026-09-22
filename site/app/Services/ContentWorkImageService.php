<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ContentWork;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;
use Throwable;

final class ContentWorkImageService
{
    public function save(ContentWork $record, array $data): ContentWork
    {
        try {
            return Cache::store('file')->lock('content-work-upload', 120)->block(5, fn () => $this->saveUnderLock($record, $data));
        } catch (LockTimeoutException) {
            $this->invalid('Another image is being processed. Please wait a moment, then save again.');
        }
    }

    private function saveUnderLock(ContentWork $record, array $data): ContentWork
    {
        $upload = $data['image_upload'] ?? null;
        if (is_array($upload)) {
            $upload = Arr::first($upload);
            $data['image_upload'] = $upload;
        }

        $rules = [
            'discipline' => ['required', Rule::in(['photography', 'design'])],
            'title' => ['required', 'string', 'max:120'],
            'client' => ['nullable', 'string', 'max:120'],
            'description' => ['required', 'string', 'max:500'],
            'image_alt' => ['required', 'string', 'max:180'],
            'link_url' => ['nullable', 'url:http,https', 'max:500'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:100000'],
            'is_published' => ['required', 'boolean'],
            'image_upload' => [$record->exists ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:'.config('content-works.max_upload_kb'), 'dimensions:min_width=480,min_height=320,max_width=8000,max_height=8000'],
        ];
        if (! $record->exists || $upload) {
            $rules['rights_confirmed'] = ['accepted'];
        }
        Validator::make(['data' => $data], collect($rules)->mapWithKeys(fn ($rule, $key) => ['data.'.$key => $rule])->all())->validate();

        $disk = Storage::disk('public');
        $old = [$record->image_path, $record->thumbnail_path];
        $created = [];
        $media = [];

        try {
            if ($upload instanceof UploadedFile) {
                $dimensions = @getimagesize($upload->getRealPath());
                if (! $dimensions || config('content-works.max_pixels') < $dimensions[0] * $dimensions[1]) {
                    $this->invalid('Choose an image no larger than 40 megapixels.');
                }
                if (! $this->imageMagickBinary()) {
                    $this->invalid('Image optimization is unavailable. Ask your developer to check ImageMagick before uploading.');
                }

                $reserve = max((int) $upload->getSize(), 4 * 1024 * 1024);
                if ($this->storageBytes() + $reserve > config('content-works.storage_limit_mb') * 1024 * 1024) {
                    $this->invalid('The creative-work library is full. Permanently delete unused images from Trash, or ask your developer to increase storage.');
                }
                $disk->makeDirectory('content-works');
                $free = disk_free_space($disk->path('content-works'));
                if ($free !== false && $free < $reserve + 256 * 1024 * 1024) {
                    $this->invalid('The server is low on free space. Please contact your developer before uploading.');
                }

                $uuid = (string) Str::uuid();
                $media['image_path'] = "content-works/{$uuid}-large.webp";
                $media['thumbnail_path'] = "content-works/{$uuid}-card.webp";
                $created = [$media['image_path'], $media['thumbnail_path']];
                $this->convert($upload->getRealPath(), $disk->path($media['image_path']), '1800x1800>');
                $this->convert($upload->getRealPath(), $disk->path($media['thumbnail_path']), '900x900>');

                $outputDimensions = @getimagesize($disk->path($media['image_path']));
                if (! $outputDimensions || ! $disk->exists($media['thumbnail_path'])) {
                    $this->invalid('The optimized image could not be created. Please try another JPG, PNG, or WebP file.');
                }
                $media['width'] = $outputDimensions[0];
                $media['height'] = $outputDimensions[1];
                $media['size_bytes'] = $disk->size($media['image_path']) + $disk->size($media['thumbnail_path']);
            }

            DB::transaction(function () use ($record, $data, $media, $old): void {
                $record->fill(Arr::only($data, ['discipline', 'title', 'client', 'description', 'image_alt', 'link_url', 'sort_order', 'is_published']));
                foreach ($media as $key => $value) {
                    $record->setAttribute($key, $value);
                }
                $record->save();
                DB::afterCommit(fn () => $this->deleteUnused($old));
            });

            return $record;
        } catch (Throwable $e) {
            $disk->delete($created);
            if ($e instanceof ValidationException) {
                throw $e;
            }
            report($e);
            $this->invalid('The image could not be saved. Your previous image has been kept. Please try again.');
        }
    }

    private function convert(string $source, string $target, string $geometry): void
    {
        $binary = $this->imageMagickBinary();
        if (! $binary) {
            $this->invalid('Image optimization is unavailable. Ask your developer to check ImageMagick before uploading.');
        }

        $process = new Process([
            $binary, $source, '-auto-orient', '-strip', '-resize', $geometry,
            '-colorspace', 'sRGB', '-quality', '82', '-define', 'webp:method=5', $target,
        ]);
        $process->setTimeout(45);
        $process->mustRun();
    }

    private function imageMagickBinary(): ?string
    {
        $configured = config('content-works.magick');
        if (is_string($configured) && is_executable($configured)) {
            return $configured;
        }

        return (new ExecutableFinder)->find('magick') ?? (new ExecutableFinder)->find('convert');
    }

    public function storageBytes(): int
    {
        $disk = Storage::disk('public');

        return (int) collect($disk->allFiles('content-works'))->sum(fn ($path) => $disk->size($path));
    }

    public function deleteUnused(array $paths): void
    {
        try {
            foreach (array_filter($paths) as $path) {
                if (! preg_match('/\Acontent-works\/[a-f0-9-]{36}-(large|card)\.webp\z/', $path)) {
                    continue;
                }
                if (! ContentWork::withTrashed()->where('image_path', $path)->orWhere('thumbnail_path', $path)->exists()) {
                    Storage::disk('public')->delete($path);
                }
            }
        } catch (Throwable $e) {
            report($e);
        }
    }

    private function invalid(string $message): never
    {
        throw ValidationException::withMessages(['data.image_upload' => $message]);
    }
}
