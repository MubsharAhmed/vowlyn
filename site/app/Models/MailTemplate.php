<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\ServiceCatalog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * A written reply the studio can send from the admin panel.
 *
 * One template per service ("profession") keeps the first draft specific to
 * what the lead actually asked about, and more can be added at any time —
 * either as another variant for the same service or for a service that does
 * not exist yet.
 *
 * Bodies are plain text with a few tokens. Tokens are substituted before the
 * message is rendered and escaped at render time, so a lead's own name can
 * never inject markup into the email.
 */
final class MailTemplate extends Model
{
    public const TOKENS = ['{name}', '{company}', '{service}', '{studio}'];

    protected $fillable = ['service', 'name', 'subject', 'body', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    /**
     * @param  Builder<MailTemplate>  $query
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * @param  Builder<MailTemplate>  $query
     */
    public function scopeForService(Builder $query, ?string $service): void
    {
        $service === null
            ? $query->whereNull('service')
            : $query->where('service', $service);
    }

    /**
     * Templates that apply to a lead: written for their service first, then any
     * general replies, then everything else so a one-off service is still served.
     *
     * @return Collection<int, MailTemplate>
     */
    public static function choicesFor(?string $service): Collection
    {
        $all = self::query()->active()->orderBy('name')->get();

        return $all->sortBy(fn (self $template): int => match (true) {
            $service !== null && $template->service === $service => 0,
            $template->service === null => 1,
            default => 2,
        })->values();
    }

    public static function defaultFor(?string $service): ?self
    {
        return self::choicesFor($service)->first();
    }

    public function serviceLabel(): string
    {
        if ($this->service === null) {
            return 'Any service';
        }

        return ServiceCatalog::options()[$this->service] ?? Str::headline($this->service);
    }

    /**
     * @param  array<string, string>  $tokens
     */
    public function renderSubject(array $tokens): string
    {
        return $this->interpolate($this->subject, $tokens);
    }

    /**
     * @param  array<string, string>  $tokens
     */
    public function renderBody(array $tokens): string
    {
        return $this->interpolate($this->body, $tokens);
    }

    /**
     * strtr() replaces each token exactly once, so a value that happens to
     * contain another token is never re-substituted.
     *
     * @param  array<string, string>  $tokens
     */
    private function interpolate(string $value, array $tokens): string
    {
        return strtr($value, $tokens);
    }
}
