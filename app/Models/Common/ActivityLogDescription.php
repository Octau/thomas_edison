<?php

namespace App\Models\Common;

use App\Models\Abstract\BaseEnum;

class ActivityLogDescription extends BaseEnum
{
    public const STORE = 'store';

    public const UPDATE = 'update';

    public const DESTROY = 'destroy';

    public static function eventMap(string $eventName): string
    {
        return match ($eventName) {
            'created' => self::STORE,
            'updated' => self::UPDATE,
            'deleted' => self::DESTROY,
            default => $eventName,
        };
    }
}
