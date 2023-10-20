<?php

namespace App\Models;

use App\Enums\Format;
use App\Casts\DateTime;

use App\Models\ReportLayout;
use App\Traits\SensesModel;
use Illuminate\Database\Eloquent\Model;
use Silber\Bouncer\Database\Role as BouncerRole;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends BouncerRole
{
    use SensesModel;

    static $disableHiddenFields = true;
    public $uuids = false;

    // public function getObjectAttribute()
    // {
    //     return 'roles';
    // }

    protected $fillable = [
        'name',
		'title',
        'locked_at',
        'lock_type'
	];

    protected $casts = [
		'locked_at' => DateTime::class,
		'created_at' => DateTime::class,
		'updated_at' => DateTime::class,
		'deleted_at' => DateTime::class,
		'hidden_at' => DateTime::class,
	];

    public function scopeTableSearch($query, $search) {
        $table = $this->getTable();
        $query->where(function($q) use(&$search, $table) {
            $q->where( $table.'.id', 'like', '%'. $search .'%');
            $q->orWhere($table.'.title', 'ilike', '%'. $search.'%');
        });
    }

    public function allowedSorts()
    {
        return ['id', 'title'];
    }

    public function allowedEmbeds()
    {
        return [];
    }

    public function allowedFields()
    {
        return ['id', 'title'];
    }

    public function allowedFilters() {
        return $this->defineFilters([
            'id' => 'integer',
            'title' => 'text'
        ]);
    }

    public function AbilityGroups() {
        return $this->belongsToMany(AbilityGroup::class)->withTimestamps();
    }

    public function reportLayouts(): BelongsToMany
    {
        return $this->belongsToMany(ReportLayout::class)->withTimestamps();
    }

}

//Generated 12-01-2022 10:58:58
