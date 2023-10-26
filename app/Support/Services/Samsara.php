<?php

namespace App\Support\Services;

use App\Models\Asset;
use App\Models\Depot;
use App\Models\AssetOwnership;
use Illuminate\Support\Carbon;

use App\Models\AssignmentGroup;
use Illuminate\Support\Facades\Http;
use App\Interfaces\Services\TrackerService;

class Samsara implements TrackerService
{
    // ---------------- KEY -----------------
    // A Route is the whole travel for the day
    // A Journey is from Landmark to Landmark
    // A Landmark is a known location (Assignment Group, Depot or Driver's House)
    // A Trip is from Stop to Stop
    // A Stop is a place they have been for over 5 minutes

    // Journeys can be made from many trips
    // Routes are made from many journeys and trips.

    public function getTimeline(int|Asset $asset, string|Carbon $startDate = null, string|Carbon $endDate = null) : array
    {
        $asset = $this->formatAsset($asset);
        $startDate = $this->formatDate($startDate, "start");
        $endDate = $this->formatDate($endDate, "end");

        if ($startDate && $endDate && $endDate->lt($startDate)) {
            $endDate->addDay();
        }

        $samsaraTrips = $this->getFormattedTrips($asset, $startDate, $endDate);
        $fuelStops = $this->getFuelStops($asset, $startDate, $endDate);
        $safetyEvents = $this->getSafetyEvents($asset, $startDate, $endDate);

        $journey = [];
        $journeys = [];

        foreach ($samsaraTrips as $index => $trip) {
            $trip["safetyEventsCount"] = 0;
            $trip["safetyEvents"] = [];

            foreach ($safetyEvents as $safetyEvent) {
                $eventTime = Carbon::parse($safetyEvent["time"])->format('Y-m-d H:i:s');
                if ($trip["startedAt"] < $eventTime && $eventTime < $trip["endedAt"]) {
                    $trip["safetyEventsCount"]++;
                    $trip["safetyEvents"][] = [
                        "id" => $safetyEvent["id"],
                        "time" => $eventTime,
                        "maxGForce" => $safetyEvent["maxAccelerationGForce"],
                        "forwardVideoUrl" => $safetyEvent["downloadForwardVideoUrl"] ?? $safetyEvent,
                        "inwardVideoUrl" => $safetyEvent["downloadInwardVideoUrl"] ?? $safetyEvent,
                        "latitude" => $safetyEvent["location"]["latitude"],
                        "longitude" => $safetyEvent["location"]["longitude"],
                        "name" => $safetyEvent["behaviorLabels"][0]["name"],
                        "type" => $safetyEvent["behaviorLabels"][0]["label"],
                    ];
                }
            }

            $journey[] = $trip;

            if ((isset($trip["endLocation"])) || $index == count($samsaraTrips) - 1) {
                if (isset($trip["endLocation"]) && $trip["endLocation"]["type"] == "assignment-group" && !isset($journeys[$trip["endLocation"]["name"]])) {
                    $journeys[$trip["endLocation"]["name"]]["trips"] = $journey;
                } else if (!isset($journeys["return"]) && count($journeys) > 0 && !in_array("assignment-group", array_column(array_column(array_slice($samsaraTrips, $index), "endLocation"), "type"))) {
                    $journeys["return"]["trips"] = $journey;
                } else {
                    foreach ($journey as $trip) {
                        $journeys["misc"]["trips"][] = $trip;
                    }
                }

                $journey = [];
            }
        }

        foreach ($journeys as $name => $journey) {
            if ($name != "misc") {
                $firstTrip = $journey["trips"][0];
                $lastTrip = $journey["trips"][count($journey["trips"]) - 1];

                $journey["startedAt"] = $firstTrip["startedAt"];
                $journey["startAddress"] = $firstTrip["startAddress"];
                $journey["startLocation"] = $firstTrip["startLocation"] ?? null;

                $journey["endedAt"] = $lastTrip["endedAt"];
                $journey["endAddress"] = $lastTrip["endAddress"];
                $journey["endLocation"] = $lastTrip["endLocation"] ?? null;

                $journey["duration"] = Carbon::parse($journey["startedAt"])->diffInMinutes($journey["endedAt"]);
                $journey["distance"] = array_sum(array_column($journey["trips"], "distance"));
            }

            $journey["stops"] = [];
            $journey["stopsCount"] = 0;
            $journey["stopsDurationTotal"] = 0;

            $journey["safetyEvents"] = [];
            $journey["safetyEventsCount"] = 0;

            foreach ($journey["trips"] as $tripIndex => $trip) {
                foreach ($trip["safetyEvents"] as $safetyEvent) {
                    $journey["safetyEvents"][] = $safetyEvent;
                    $journey["safetyEventsCount"]++;
                }

                if ($tripIndex > 0) {
                    $previousTrip = $journey["trips"][$tripIndex - 1];
                    if ($trip != $previousTrip) {
                        $stopDuration = Carbon::parse($previousTrip["endedAt"])->diffInMinutes($trip["startedAt"]);

                        $stopType = "invalid-stop";
                        foreach ($fuelStops as $fuelStop) {
                            if ($previousTrip["endedAt"] < $fuelStop && $fuelStop < $trip["startedAt"]) {
                                $stopType = "fuel-stop";
                            }
                        }

                        $stopData = [
                            "startedAt" => $previousTrip["endedAt"],
                            "endedAt" => $trip["startedAt"],
                            "duration" => $stopDuration,
                            "address" => $previousTrip["endAddress"],
                            "latitude" => $previousTrip["endCoordinates"][0],
                            "longitude" => $previousTrip["endCoordinates"][1],
                            "type" => $stopType,
                        ];

                        $journey["stops"][] = $stopData;
                        $journey["stopsCount"]++;
                        $journey["stopsDurationTotal"] += $stopDuration;
                        if (isset($journey["duration"])) {
                            $journey["duration"] -= $stopDuration; // TODO: Decide if this should be included or not
                        }
                    }
                }
            }

            $journeys[$name] = $journey;
        }

        return $journeys;
    }

    public function getFormattedTrips(int|Asset $asset, string|Carbon $startDate = null, string|Carbon $endDate = null) : array
    {
        $asset = $this->formatAsset($asset);
        $startDate = $this->formatDate($startDate, "start");
        $endDate = $this->formatDate($endDate, "end");

        if ($startDate && $endDate && $endDate->lt($startDate)) {
            $endDate->addDay();
        }

        if (!isset($asset->tracking_id)) {
            return [];
        }

        $response = Http::timeout(30)->withToken(env('SAMSARA_API_KEY'))->get("https://api.eu.samsara.com/v1/fleet/trips", [
            'vehicleId' => $asset->tracking_id,
            'startMs' => $startDate->getPreciseTimestamp(3),
            'endMs' => $endDate->getPreciseTimestamp(3),
        ]);

        // Doing this adjusts the startDate to accomodate the start of the trip in the case that a trip starts before the config startDate
        if (count($response["trips"] ?? []) > 0) {
            $startDate = Carbon::createFromTimestampMs($response["trips"][0]["startMs"]);
        }

        $data = $this->getAllPages("https://api.eu.samsara.com/fleet/vehicles/stats/history", [
            'vehicleIds' => $asset->tracking_id,
            'startTime' => $startDate->toRfc3339String(),
            'endTime' => $endDate->toRfc3339String(),
            'types' => 'gps'
        ]);

        $landmarks = $this->getLandmarks($asset, $startDate, $endDate);

        $trips = [];
        foreach ($response["trips"] as $trip) {
            if ($trip["endMs"] < 9223372036854000000) { // TODO: This is because the unfinished routes were causing issues at the time, Should probably do something better than just ignoring them though
                $tripStartDate = Carbon::createFromTimestampMs($trip['startMs']);
                $tripEndDate = Carbon::createFromTimestampMs($trip['endMs']);

                if ($tripEndDate->gt($endDate)) {
                    continue;
                }

                $gpsPoints = [];
                foreach ($data[0]["gps"] as $gps) {
                    $gpsTime = Carbon::createFromTimestamp(Carbon::parse($gps["time"])->timestamp);

                    if ($gpsTime->lt($tripEndDate) && $gpsTime->gt($tripStartDate)) {
                        $gpsPoints[] = [
                            "latitude" => $gps["latitude"],
                            "longitude" => $gps["longitude"],
                            "time" => $gps["time"],
                        ];
                    }
                }
            }

            $trip = [
                'startedAt' => $tripStartDate->format('Y-m-d H:i:s'),
                'startAddress' => $trip["startLocation"],
                'startCoordinates' => array_values($trip["startCoordinates"]),

                'endedAt' => $tripEndDate->format('Y-m-d H:i:s'),
                'endAddress' => $trip["endLocation"],
                'endCoordinates' => array_values($trip["endCoordinates"]),

                'distance' => $trip["distanceMeters"],
                'duration' => $tripStartDate->diffInMinutes($tripEndDate),
                'fuelUsed' => $trip['fuelConsumedMl'],
                'odometer' => intdiv($trip['endOdometer'], 1000),

                'gps' => $gpsPoints,
            ];

            foreach ($landmarks as $poi) {
                if (isset($poi["coords"][0]) && $poi["coords"][1]) {
                    if ($this->isCloseTo($poi["coords"], $trip["startCoordinates"])) {
                        $trip["startLocation"] = $poi;
                    }
                    if ($this->isCloseTo($poi["coords"], $trip["endCoordinates"])) {
                        $trip["endLocation"] = $poi;
                    }
                } else {
                    $poiName = $poi["name"];
                    echo "Error: $poiName has no coordinates";
                }
            }

            $trips[] = $trip;
        }

        return $trips;
    }

    /* ##################################################### HELPERS #################################################### */

    private function getSafetyEvents($asset, $startDate, $endDate)
    {
        $data = $this->getAllPages("https://api.eu.samsara.com/fleet/safety-events", [
            'vehicleIds' => $asset->tracking_id,
            'startTime' => $startDate->toRfc3339String(),
            'endTime' => $endDate->toRfc3339String(),
        ]);

        return $data;
    }

    private function getFuelStops($asset, $startDate, $endDate)
    {
        $data = $this->getAllPages("https://api.eu.samsara.com/fleet/vehicles/stats/history", [
            'vehicleIds' => $asset->tracking_id,
            'startTime' => $startDate->toRfc3339String(),
            'endTime' => $endDate->toRfc3339String(),
            'types' => 'fuelPercents'
        ]);

        if (empty($data)) {
            return [];
        }

        $fuelStops = [];

        $fuelPercents = collect($data[0]["fuelPercents"]);
        $fuelPercents->sliding(2)->each(function ($pair) use (&$fuelStops) {
            $pair = $pair->values();
            if ($pair[0]["value"] - $pair[1]["value"] < -10) {
                $fuelStops[] = Carbon::createFromTimestamp(Carbon::parse($pair[1]["time"])->timestamp)->format('Y-m-d H:i:s');
            }
        });

        return $fuelStops;
    }

    private function getAllPages($url, $params = [])
    {
        $data = [];

        $hasNextPage = true;
        while ($hasNextPage) {
            $response = Http::timeout(30)->withToken(env('SAMSARA_API_KEY'))->get($url, $params);

            $data = array_merge($data, $response["data"]);

            $hasNextPage = $response['pagination']['hasNextPage'];
            $params['after'] = $response['pagination']['endCursor'];
        }

        return $data;
    }

    private function getLandmarks($asset, $startDate, $endDate)
    {
        $landmarks = [];

        $startDate = $this->formatDate($startDate);
        $endDate = $this->formatDate($endDate);

        if ($startDate && $endDate && $endDate->lt($startDate)) {
            $endDate->addDay();
        }

        $depots = Depot::all();

        foreach ($depots as $depot) {
            if (isset($depot->geom)) {
                $landmarks[] = [
                    "name" => "$depot->title Depot",
                    "type" => "depot",
                    "id" => $depot->id,
                    "coords" => $depot->geom->getRawArray(),
                ];
            }
        }

        $assetAssignmentGroups = AssignmentGroup::whereHas('assignments', function ($q) use ($asset) {
            $q->whereAssignable($asset);
        })->whereBetween('start_date', [$startDate, $endDate])->get();

        foreach ($assetAssignmentGroups as $assignmentGroup) {
            if (isset($assignmentGroup?->geom)) {
                $landmarks[] = [
                    "name" => "Assignment Group {$assignmentGroup->id}",
                    "type" => "assignment-group",
                    "id" => $assignmentGroup->id,
                    "coords" => $assignmentGroup?->geom?->getRawArray(),
                ];
            }
        }

        $currentOwnership = AssetOwnership::WhereOwnedBy($asset->id, $startDate->copy())->oldest()->first();
        $assetStartingPointGeom = $currentOwnership->geom ?? false;

        if ($assetStartingPointGeom) {
            // dump($assetStartingPointGeom);
            $landmarks[] = [
                "name" => "Asset Starting Point",
                "type" => $asset->currentAssetOwnership->object,
                "id" => $asset->currentAssetOwnership->id,
                "coords" => $assetStartingPointGeom->getRawArray(),
            ];
        }

        return $landmarks;
    }

    private function getCoordsFromGPS($gps)
    {
        $lng = $gps['longitude'];
        $lat = $gps['latitude'];
        $coords = [$lat, $lng];

        return $coords;
    }

    private function isCloseTo($point1, $point2)
    {
        if (abs($point1[0] - $point2[0]) < 0.004) {
            if (abs($point1[1] - $point2[1]) < 0.006) {
                return true;
            }
        }
        return false;
    }

    private function formatAsset($asset)
    {
        if (is_int($asset) || is_string($asset)) {
            $asset = Asset::findOrFail($asset);
        }

        return $asset;
    }

    private function formatDate($date, $type = "start")
    {
        // $type = "start" || "end"

        if (is_string($date)) {
            $date = Carbon::parse($date);
        }

        if (!$date) {
            $date = now();
            if ($type == "start") {
                $configStartTime = config('client.scheduler.day_modes.all.start_time', '00:00:00');
                $date = $date->copy()->setTimeFromTimeString($configStartTime);
            }

            if ($type == "end") {
                $configEndTime = config('client.scheduler.day_modes.all.end_time', '23:59:59');
                $date = $date->copy()->setTimeFromTimeString($configEndTime);
            }
        }

        return $date;
    }
}
