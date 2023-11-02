<?php

namespace App\Http\Helpers\QueryBuilderHelpers;

use Illuminate\Http\Request;
use Str;

class FilterComposer
{
    public const TypeText = 'text';
    public const TypeOption = 'option';
    public const TypeDate = 'date';
    public const TypeNumber = 'number';

    public const TextBehaviourPartial = 'partial';
    public const TextBehaviourExact = 'exact';

    public const OptionBehaviourSingle = 'single';
    public const OptionBehaviourMultiple = 'multiple';

    public const DateBehaviourExact = 'exact';
    public const DateBehaviourBefore = 'before';
    public const DateBehaviourAfter = 'after';
    public const DateBehaviourRange = 'range';

    public static function getFilter(array $filters, Request $request, array $options = []): array
    {
        return array_map(function ($item) use ($request, $options) {
            $default = null;
            count(explode('.', $item)) === 3
                ? [$className, $name, $default] = explode('.', $item)
                : [$className, $name] = explode('.', $item);

            if (isset($options[$name])) {
                return self::$className($name, $options[$name], $request->input("filter.$name"), $default);
            } else {
                return self::$className($name, $request->input("filter.$name"), $default);
            }
        }, $filters);
    }

    public static function exactText(string $name, ?string $value, ?string $default): array
    {
        return [
            'name' => $name,
            'label' => self::getLabel($name),
            'type' => self::TypeText,
            'behaviour' => self::TextBehaviourExact,
            'value' => $value,
            'default' => $default,
        ];
    }

    public static function partialText(string $name, ?string $value, ?string $default): array
    {
        return [
            'name' => $name,
            'label' => self::getLabel($name),
            'type' => self::TypeText,
            'behaviour' => self::TextBehaviourPartial,
            'value' => $value,
            'default' => $default,
        ];
    }

    public static function exactDate(string $name, ?string $value, ?string $default): array
    {
        return [
            'name' => $name,
            'label' => self::getLabel($name),
            'type' => self::TypeDate,
            'behaviour' => self::DateBehaviourExact,
            'value' => $value,
            'default' => $default,
        ];
    }

    public static function afterDate(string $name, ?string $value, ?string $default): array
    {
        return [
            'name' => $name,
            'label' => self::getLabel($name),
            'type' => self::TypeDate,
            'behaviour' => self::DateBehaviourAfter,
            'value' => $value,
            'default' => $default,
        ];
    }

    public static function beforeDate(string $name, ?string $value, ?string $default): array
    {
        return [
            'name' => $name,
            'label' => self::getLabel($name),
            'type' => self::TypeDate,
            'behaviour' => self::DateBehaviourBefore,
            'value' => $value,
            'default' => $default,
        ];
    }

    public static function rangeDate(string $name, ?string $value, ?string $default): array
    {
        return [
            'name' => $name,
            'label' => self::getLabel($name),
            'type' => self::TypeDate,
            'behaviour' => self::DateBehaviourRange,
            'value' => $value,
            'default' => $default,
        ];
    }

    public static function singleOption(string $name, array $options, ?string $value, ?string $default): array
    {
        return [
            'name' => $name,
            'label' => self::getLabel($name),
            'type' => self::TypeOption,
            'options' => $options,
            'behaviour' => self::OptionBehaviourSingle,
            'value' => $value,
            'default' => $default,
        ];
    }

    public static function multipleOption(string $name, array $options, ?string $value, ?string $default): array
    {
        return [
            'name' => $name,
            'label' => self::getLabel($name),
            'type' => self::TypeOption,
            'options' => $options,
            'behaviour' => self::OptionBehaviourMultiple,
            'value' => $value,
            'default' => $default,
        ];
    }

    private static function getLabel(string $name): string
    {
        return Str::title(str_replace('_', ' ', $name));
    }
}
