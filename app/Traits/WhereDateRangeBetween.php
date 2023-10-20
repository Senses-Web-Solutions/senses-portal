<?php
namespace App\Traits;

use Carbon\Carbon;

trait WhereDateRangeBetween
{
    public function scopeWhereDateRangeBetween($query, Carbon $startDate, Carbon $endDate, $startField = 'start_date', $endField = 'end_date')
    {
        return $query->where($startField, '<=', $endDate)->where($endField, '>=', $startDate);
    }
}
