<?php

namespace App\Traits;

use App\Support\RouteData;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

trait Schedulable
{
    abstract public function toSchedule(string $type = null): array;

    public function getScheduleTypeAttribute(): string
    {
        return Str::kebab(class_basename($this));
    }

    public function getScheduleIDAttribute(): int
    {
        return $this->id;
    }

    abstract public function getScheduleOvernightAttribute(): bool;
    abstract public function getScheduleOrderedAttribute(): bool;

    public function getLines(string $type)
    {
        $lineTypes = config('client.scheduler.' . $type . '.lines', []);
        $lines = [];
        foreach ($lineTypes as $lineType) {
            $line = null;
            if (method_exists($this, $lineType)) {
                $line = $this->$lineType();
            }

            array_push($lines, $line ? Str::limit($line) : $line);
        }

        $lines = $this->removeTrailingNull($lines);

        return $lines;
    }

    private function removeTrailingNull(array $array)
    {
        if ($array[count($array) - 1] === null) {
            array_pop($array);
            return $this->removeTrailingNull($array);
        }
        return $array;
    }

    public function getRouteScheduleData() : RouteData {
        return new RouteData();
    }
}
