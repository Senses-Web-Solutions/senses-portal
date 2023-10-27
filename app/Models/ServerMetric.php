<?php

namespace App\Models;

use App\Traits\SensesModel;

use Illuminate\Database\Eloquent\Model;
use App\Enums\Format;
use App\Casts\DateTime;
use App\Casts\Money;
use App\Casts\Date;

class ServerMetric extends Model
{
    use SensesModel;

    
    protected $fillable = [
		'app_id',
		'timestamp', 
		'uptime', 
		'logged_at', 
		'cpu_cores', 
		'cpu_threads', 
		'cpu_use', 
		'cpu_idle', 
		'load_1', 
		'load_5', 
		'load_15', 
		'ram_free', 
		'ram_used', 
		'disk_free', 
		'disk_used', 
		'swap_free', 
		'swap_used'
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
        return ['id', 'timestamp', 'uptime', 'logged_at', 'cpu_cores', 'cpu_threads', 'cpu_use', 'cpu_idle', 'load_1', 'load_5', 'load_15', 'ram_free', 'ram_used', 'disk_free', 'disk_used', 'swap_free', 'swap_used'];
    }

    public function allowedEmbeds()
    {
        return [];
    }

    public function allowedFields()
    {
        return ['id', 'timestamp', 'uptime', 'logged_at', 'cpu_cores', 'cpu_threads', 'cpu_use', 'cpu_idle', 'load_1', 'load_5', 'load_15', 'ram_free', 'ram_used', 'disk_free', 'disk_used', 'swap_free', 'swap_used'];
    }

    public function allowedFilters() {
        return $this->defineFilters([
            'id' => 'integer',
            'timestamp' => 'text', 
			'uptime' => 'text', 
			'logged_at' => 'text', 
			'cpu_cores' => 'text', 
			'cpu_threads' => 'text', 
			'cpu_use' => 'text', 
			'cpu_idle' => 'text', 
			'load_1' => 'text', 
			'load_5' => 'text', 
			'load_15' => 'text', 
			'ram_free' => 'text', 
			'ram_used' => 'text', 
			'disk_free' => 'text', 
			'disk_used' => 'text', 
			'swap_free' => 'text', 
			'swap_used' => 'text'
        ]);
    }

	public function company()
	{
		abort(501, 'Server Metric: Company Not implemented');
		//return $this->belongsTo(Company::class);
	}

	public function server()
	{
		abort(501, 'Server Metric: Server Not implemented');
		//return $this->belongsTo(Server::class);
	}

}

//Generated 27-10-2023 10:55:27
