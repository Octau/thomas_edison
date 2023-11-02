<?php

namespace App\Http\Helpers\QueryBuilderHelpers;

use Illuminate\Support\Str;
use ReflectionClass;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\Enums\SortDirection;

class SortComposer
{
    public static function getSort(array $options, AllowedSort|string $default, ?string $value): array
    {
        if ($default instanceof AllowedSort) {
            $default = self::getDefaultName($default);
        }

        return [
            'options' => $options,
            'default' => $default,
            'value' => $value,
        ];
    }

    public static function getSortWithIcon(array $options, array $icons, AllowedSort|string $default, ?string $value): array
    {
        if ($default instanceof AllowedSort) {
            $default = self::getDefaultName($default);
        }

        return [
            'options' => array_map(static function ($label, $icon) {
                return [
                    'label' => self::getLabel($label),
                    'value' => $label,
                    'icon' => $icon,
                ];
            }, $options, $icons),
            'default' => $default,
            'value' => $value,
        ];
    }

    private static function getLabel(string $name): string
    {
        return Str::title(str_replace('_', ' ', $name));
    }

    private static function getDefaultName(AllowedSort $allowedSort): string
    {
        $reflection = new ReflectionClass(AllowedSort::class);
        $property = $reflection->getProperty('defaultDirection');
        $property->setAccessible(true);
        $direction = $property->getValue($allowedSort);

        return ($direction === SortDirection::DESCENDING ? '-' : '') . $allowedSort->getName();
    }
}
