<?php

namespace App\Enums;

/** @psalm-immutable */
enum TransactionStatus: string
{
    use Enum;
    
    case PENDING = 'pending';
    case IN_PROGRESS = 'in-progress';
    case SUCCESS = 'success';
    case FAILURE = 'failure';
}
