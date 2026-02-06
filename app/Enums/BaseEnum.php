<?php

namespace App\Enums;

trait BaseEnum
{
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function contains(?string $value): bool
    {
        return in_array($value, self::values(), true);
    }

    public static function randomValue(): string
    {
        $arr = self::values();

        return $arr[array_rand($arr)];
    }

    public function equals(self $enum): bool
    {
        return $this === $enum;
    }
}


