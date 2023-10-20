<?php

namespace App\Traits;

use App\Support\RouteData;
use Illuminate\Database\Eloquent\Model;
use Shapefile\Geometry\Point;

trait Routable
{

    public function getRouteLines() : array {
        return [];
    }

    public function getRouteAddress() : array {
        $addressProperties = ['name', 'street', 'town', 'city', 'country', 'postcode'];
        $address = [];
        foreach($addressProperties as $addressProperty) {
            if($this->$addressProperty) {
                array_push($address, $this->$addressProperty);
            }
        }
        return $address;
    }

    public function getRouteData() : RouteData  {
        $coordinates = $this->geom && $this->geom instanceof Point ? $this->geom : null;
        return new RouteData(id:$this->id, type:$this->object, coordinates:$coordinates, lines:$this->getRouteLines());
    }

    public function getMergedRouteData() : RouteData {
        $routeData = $this->getRouteData();

        if($this->originalWaypoint && method_exists($this->originalWaypoint, 'getRouteScheduleData')) {
            $originalRouteData = $this->originalWaypoint->getRouteScheduleData();

            $routeData->scheduleID = $this->originalWaypoint->id;
            $routeData->scheduleType = $this->originalWaypoint->object;
            $routeData->scheduleLines = $originalRouteData->lines;
            $routeData->travelStartDate = $originalRouteData->travelStartDate;
            $routeData->travelEndDate = $originalRouteData->travelEndDate;
            $routeData->siteStartDate = $originalRouteData->siteStartDate;
            $routeData->siteEndDate = $originalRouteData->siteEndDate;
            $routeData->meta = $originalRouteData->meta;
            $routeData->duration = $originalRouteData->duration;
            $routeData->distance = $originalRouteData->distance;            
        }

        return $routeData;
    }

    
}