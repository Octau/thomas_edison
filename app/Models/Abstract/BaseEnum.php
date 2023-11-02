<?php

namespace App\Models\Abstract;

use BenSampo\Enum\Enum;

abstract class BaseEnum extends Enum
{
    public static function toSelectOption(?array $options = null): array
    {
        return array_map(static function ($option) {
            return [
                'label' => self::getDescription($option),
                'value' => $option
            ];
        }, $options ?? self::getValues());
    }
}

