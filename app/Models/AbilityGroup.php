<?php

namespace App\Models;

use App\Traits\SensesModel;
use Illuminate\Database\Eloquent\Model;

class AbilityGroup extends Model
{
    use SensesModel;

    public $actionLogsEnabled = false;
    protected $fillable = ['title'];

    public function scopeTableSearch($query, $search) {
        $table = $this->getTable();
        $query->where(function($q) use(&$search, $table) {
            $q->where( $table.'.id', 'like', '%'. $search .'%');
            $q->orWhere($table.'.title', 'ilike', '%'. $search.'%');
        });
    }

    public function abilities() {
        return $this->belongsToMany(Ability::class)->withTimestamps();
    }

    public function roles() {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function allowedSorts() {
        return ['id', 'title'];
    }

    public function allowedFields() {
        return ['id', 'title'];
    }

    public function allowedFilters() {
        return $this->defineFilters([
            'id' => 'integer',
            'title' => 'text'
        ]);
    }
}
