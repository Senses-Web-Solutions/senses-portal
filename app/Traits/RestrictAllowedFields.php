<?php 

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Arr;

trait RestrictAllowedFields {
    public function restrictAllowedFields(User $user, array $requestedFields = [], array $abilityFields = []) {

        $allAbilities = array_keys($abilityFields);
        if(empty($requestedFields)) {
            return $this->restrictAllFields($user, $requestedFields, $allAbilities);
        }

        return $this->restrictAbilityFields($user, $requestedFields, $abilityFields);
    }

    public function restrictAllFields(User $user, array $requestedFields, Array|String $permissions) : Bool {
        if(empty($requestedFields)) {
            return $user->can($permissions);
        }

        return true;
    }

    public function restrictAbilityFields(User $user, array $requestedFields, array $abilityFields) {
        //check rules   [ permission => [fields covered by rule] ]
        foreach($abilityFields as $ability => $abilityFields) {

            $foundFields = array_intersect(Arr::wrap($abilityFields), $requestedFields);
           
            if(!empty($foundFields)) {
                $passed = $user->can($ability);
                if(!$passed) {
                    return false;
                }
            }
        }
        return true;
    }
}