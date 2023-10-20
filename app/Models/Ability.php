<?php

namespace App\Models;

use App\Traits\SensesModel;
use Silber\Bouncer\Database\Ability as BouncerAbility;

class Ability extends BouncerAbility
{
    use SensesModel;

    static $disableHiddenFields = true;
    public $uuids = false;
    public $actionLogsEnabled = false;

    public function abilityGroups() {
        return $this->belongsToMany(AbilityGroup::class)->withTimestamps();
    }

    public function scopeTableSearch($query, $search) {
        $table = $this->getTable();
        $query->where(function($q) use(&$search, $table) {
            $q->where( $table.'.id', 'like', '%'. $search .'%');
            $q->orWhere($table.'.title', 'ilike', '%'. $search.'%');
        });
    }

    public function allowedSorts() {
        return ['id', 'name', 'title'];
    }

    public function allowedFilters() {
        return $this->defineFilters([
            'id' => 'integer',
            'title' => 'text'
        ]);
    }
}
