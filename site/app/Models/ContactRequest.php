<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\ServiceCatalog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class ContactRequest extends Model
{
    use HasFactory;

    public const STATUS_NEW = 'new';

    public const STATUS_REVIEWED = 'reviewed';

    public const STATUS_REPLIED = 'replied';

    public const STATUS_ARCHIVED = 'archived';

    protected $fillable = [
        'name',
        'email',
        'company',
        'service',
        'brief',
        'ip_address',
        'user_agent',
        'status',
        'replied_at',
    ];

    protected $casts = [
        'replied_at' => 'datetime',
    ];

    public static function statuses(): array
    {
        return [
            self::STATUS_NEW => 'New',
            self::STATUS_REVIEWED => 'Reviewed',
            self::STATUS_REPLIED => 'Replied',
            self::STATUS_ARCHIVED => 'Archived',
        ];
    }

    /**
     * @return HasMany<ContactRequestMessage, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(ContactRequestMessage::class);
    }

    public function serviceLabel(): string
    {
        return ServiceCatalog::options()[$this->service] ?? 'your project';
    }

    /**
     * Values substituted into a reply template. Everything ends up escaped when
     * the email is rendered, so raw values are safe to place here.
     *
     * @return array<string, string>
     */
    public function replyTokens(): array
    {
        return [
            '{name}' => $this->name ?: 'there',
            '{company}' => $this->company ?: 'your team',
            '{service}' => $this->serviceLabel(),
            '{studio}' => (string) config('mail.reply_to.name'),
        ];
    }
}
