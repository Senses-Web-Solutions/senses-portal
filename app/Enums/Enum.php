<?php

namespace App\Enums;

trait Enum
{
    public static function values(): array
    {
        return collect(self::cases())->map(fn ($case) => $case->value)->toArray();
    }

    public static function toArray(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($case) => [ $case->name => $case->value ])->toArray();
    }

    public static function getLabels(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($case) => [ $case->value => str($case->value)->replace('-', ' ')->replace('_', ' ')->title()->__toString() ])->toArray();
    }
}
