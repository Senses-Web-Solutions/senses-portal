<?php

namespace App\Models;

use App\Traits\SensesModel;

use Illuminate\Database\Eloquent\Model;
use App\Enums\Format;
use App\Casts\DateTime;
use App\Casts\Money;
use App\Casts\Date;

class Revenue extends Model
{
    use SensesModel;

    
    protected $fillable = [
		'app_id',
		'revenue_date', 
		'reference', 
		'description', 
		'amount', 
		'quantity', 
		'vat', 
		'sub_total', 
		'vat_total', 
		'total'
	];

    protected $casts = [
		'locked_at' => DateTime::class,
		'created_at' => DateTime::class,
		'updated_at' => DateTime::class,
		'deleted_at' => DateTime::class,
		'hidden_at' => DateTime::class,
		'revenue_date' => DateTime::class, 
		'amount' => 'float', 
		'quantity' => 'float', 
		'vat' => 'float', 
		'sub_total' => 'float', 
		'vat_total' => 'float', 
		'total' => 'float'
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
        return ['id', 'revenue_date', 'reference', 'description', 'amount', 'quantity', 'vat', 'sub_total', 'vat_total', 'total'];
    }

    public function allowedEmbeds()
    {
        return [];
    }

    public function allowedFields()
    {
        return ['id', 'revenue_date', 'reference', 'description', 'amount', 'quantity', 'vat', 'sub_total', 'vat_total', 'total'];
    }

    public function allowedFilters() {
        return $this->defineFilters([
            'id' => 'integer',
            'revenue_date' => 'text', 
			'reference' => 'text', 
			'description' => 'text', 
			'amount' => 'text', 
			'quantity' => 'text', 
			'vat' => 'text', 
			'sub_total' => 'text', 
			'vat_total' => 'text', 
			'total' => 'text'
        ]);
    }

	public function company()
	{
		abort(501, 'Revenue: Company Not implemented');
		//return $this->belongsTo(Company::class);
	}

}

//Generated 04-11-2023 16:09:26
