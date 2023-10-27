<?php

namespace App\Models;

use App\Traits\SensesModel;

use Illuminate\Database\Eloquent\Model;
use App\Enums\Format;
use App\Casts\DateTime;
use App\Casts\Money;
use App\Casts\Date;

class Server extends Model
{
    use SensesModel;


    protected $fillable = [
		'app_id',
		'name',
		'ip',
		'os',
		'priority'
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
        return ['id', 'name', 'ip', 'os', 'priority'];
    }

    public function allowedEmbeds()
    {
        return [];
    }

    public function allowedFields()
    {
        return ['id', 'name', 'ip', 'os'];
    }

    public function allowedFilters() {
        return $this->defineFilters([
            'id' => 'integer',
            'name' => 'text',
			'ip' => 'text',
			'os' => 'text'
        ]);
    }

	public function company()
	{
		// abort(501, 'Server: Company Not implemented');
		return $this->belongsTo(Company::class);
	}

	public function serverMetrics()
	{
		// abort(501, 'Server: Server Metrics Not implemented');
		return $this->hasMany(ServerMetric::class);
	}

}

//Generated 27-10-2023 10:53:42
