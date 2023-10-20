<?php

namespace App\Enums;

/** @psalm-immutable */
enum Format: string
{
    use Enum;

    case DATETIME = 'Y-m-d H:i:s';
    case DATE = 'Y-m-d';
    case TIME = 'H:i:s';
    case TIME_SHORT = 'H:i';
}
