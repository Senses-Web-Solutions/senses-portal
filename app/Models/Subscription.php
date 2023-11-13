<?php

namespace App\Models;

use App\Traits\SensesModel;

use Illuminate\Database\Eloquent\Model;
use App\Enums\Format;
use App\Casts\DateTime;
use App\Casts\Money;
use App\Casts\Date;

class Subscription extends Model
{
    use SensesModel;


    protected $fillable = [
		'app_id',
		'type',
		'data'
	];

    protected $casts = [
		'locked_at' => DateTime::class,
		'created_at' => DateTime::class,
		'updated_at' => DateTime::class,
		'deleted_at' => DateTime::class,
		'hidden_at' => DateTime::class,
		'data' => 'array'
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
        return ['id', 'type'];
    }

    public function allowedEmbeds()
    {
        return [];
    }

    public function allowedFields()
    {
        return ['id', 'type'];
    }

    public function allowedFilters() {
        return $this->defineFilters([
            'id' => 'integer',
            'type' => 'text'
        ]);
    }

	public function company()
	{
		abort(501, 'Subscription: Company Not implemented');
		//return $this->belongsTo(Company::class);
	}

}

//Generated 04-11-2023 16:09:38
