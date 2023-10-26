<?php

namespace App\Support\Services;

use App\Interfaces\Services\TrackerService;
use App\Models\Asset;
use Illuminate\Support\Carbon;

class NullTracker implements TrackerService
{
    public function getTimeline(int|Asset $asset, string|Carbon $startDate = null, string|Carbon $endDate = null) : array
    {
        return [];
    }

    public function getFormattedTrips(int|Asset $asset, string|Carbon $startDate = null, string|Carbon $endDate = null) : array
    {
        return [];
    }
}
