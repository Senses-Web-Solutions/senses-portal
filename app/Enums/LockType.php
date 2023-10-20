<?php

namespace App\Enums;

/** @psalm-immutable */
enum LockType: string
{
    use Enum;

    case CORE = 'core';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case APPROVED = 'approved';
    case GENERATED = 'generated';
    case PAID = 'paid';
    case RETIRED = 'retired';
    case SUBMITTED = 'submitted';
    case FINANCED = 'financed';

    public static function getLabels(): array
    {
        return [
            self::CORE->value => 'Core system information',
            self::PROCESSING->value => 'Processing',
            self::COMPLETED->value => 'Completed',
            self::APPROVED->value => 'Approved',
            self::GENERATED->value => 'Generated',
            self::PAID->value => 'Paid',
            self::RETIRED->value => 'Retired',
            self::SUBMITTED->value => 'Submitted',
            self::FINANCED->value => 'Financed',
        ];
    }
}
