<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
