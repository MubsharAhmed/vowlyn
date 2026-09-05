<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\BlogPost;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

final class BlogImageService
{
    /** @var array<string, array{width:int,height:int}> */
    private const PRESETS = [
        'hero' => ['width' => 1600, 'height' => 900],
        'card' => ['width' => 800, 'height' => 450],
        'social' => ['width' => 1200, 'height' => 630],
        'landscape' => ['width' => 1200, 'height' => 900],
        'square' => ['width' => 1200, 'height' => 1200],
    ];

    public function generateFor(BlogPost $post): void
    {
        if ($post->featured_image) {
            foreach (array_keys(self::PRESETS) as $preset) {
                $this->generate((string) $post->featured_image, $preset);
            }
        }

        if ($post->social_image && $post->social_image !== $post->featured_image) {
            $this->generate((string) $post->social_image, 'social');
        }
    }

    public function urlFor(BlogPost $post, string $preset): string
    {
        $source = $preset === 'social' && $post->social_image
            ? (string) $post->social_image
            : (string) ($post->featured_image ?: $post->social_image);

        if (blank($source)) {
            return asset('brand/vowlyn-logo.png');
        }

        $conversion = $this->conversionPath($source, $preset);

        return Storage::disk('public')->exists($conversion)
            ? Storage::disk('public')->url($conversion)
            : Storage::disk('public')->url($source);
    }

    /** @return array{width:int,height:int} */
    public function dimensions(string $preset): array
    {
        return self::PRESETS[$preset] ?? self::PRESETS['hero'];
    }

    public function deleteSourceAndConversions(string $source): void
    {
        if (blank($source)) {
            return;
        }

        $paths = [$source];

        foreach (array_keys(self::PRESETS) as $preset) {
            $paths[] = $this->conversionPath($source, $preset);
        }

        Storage::disk('public')->delete($paths);
    }

    /** @param array<string, mixed>|null $content @return array<int, string> */
    public function contentImagePaths(?array $content): array
    {
        $paths = [];
        $walk = function (mixed $value, ?string $key = null) use (&$walk, &$paths): void {
            if ($key === 'path' && is_string($value) && str_starts_with($value, 'blog/content/')) {
                $paths[] = $value;

                return;
            }

            if (is_array($value)) {
                foreach ($value as $childKey => $childValue) {
                    $walk($childValue, is_string($childKey) ? $childKey : null);
                }
            }
        };

        $walk($content);

        return array_values(array_unique($paths));
    }

    /** @param array<int, string> $paths */
    public function deleteContentImages(array $paths): void
    {
        Storage::disk('public')->delete($paths);
    }

    private function generate(string $source, string $preset): void
    {
        if (! isset(self::PRESETS[$preset]) || ! Storage::disk('public')->exists($source)) {
            return;
        }

        $binary = (new ExecutableFinder)->find('magick') ?? (new ExecutableFinder)->find('convert');

        if (! $binary) {
            return;
        }

        $target = $this->conversionPath($source, $preset);
        $targetDirectory = dirname(Storage::disk('public')->path($target));

        if (! is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0775, true);
        }

        ['width' => $width, 'height' => $height] = self::PRESETS[$preset];

        $command = [
            $binary,
            Storage::disk('public')->path($source),
            '-auto-orient',
            '-strip',
            '-thumbnail', "{$width}x{$height}^",
            '-gravity', 'center',
            '-extent', "{$width}x{$height}",
            '-quality', '82',
            Storage::disk('public')->path($target),
        ];

        $process = new Process($command);
        $process->setTimeout(45);
        $process->run();
    }

    private function conversionPath(string $source, string $preset): string
    {
        $extension = $preset === 'social' ? 'jpg' : 'webp';

        return sprintf('blog/conversions/%s-%s.%s', substr(sha1($source), 0, 20), $preset, $extension);
    }
}
