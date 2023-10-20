<?php
namespace App\Traits;

trait HasOwners
{
    public function getOwnerIdsAttribute()
    {
        $ownerIDs = [];

        if(isset($this->user_id)){
            array_push($ownerIDs, $this->user_id);
        }

        if(isset($this->created_by)){
            array_push($ownerIDs, $this->created_by);
        }

        return $ownerIDs;
    }
}
