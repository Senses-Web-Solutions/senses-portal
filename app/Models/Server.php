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
		'title', 
		'hostname', 
		'ip_address', 
		'os', 
		'architecture', 
		'cpu_cores', 
		'cpu_threads', 
		'distro', 
		'distro_version', 
		'kernel', 
		'kernel_version'
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
        return ['id', 'title', 'hostname', 'ip_address', 'os', 'architecture', 'cpu_cores', 'cpu_threads', 'distro', 'distro_version', 'kernel', 'kernel_version'];
    }

    public function allowedEmbeds()
    {
        return [];
    }

    public function allowedFields()
    {
        return ['id', 'title', 'hostname', 'ip_address', 'os', 'architecture', 'cpu_cores', 'cpu_threads', 'distro', 'distro_version', 'kernel', 'kernel_version'];
    }

    public function allowedFilters() {
        return $this->defineFilters([
            'id' => 'integer',
            'title' => 'text', 
			'hostname' => 'text', 
			'ip_address' => 'text', 
			'os' => 'text', 
			'architecture' => 'text', 
			'cpu_cores' => 'text', 
			'cpu_threads' => 'text', 
			'distro' => 'text', 
			'distro_version' => 'text', 
			'kernel' => 'text', 
			'kernel_version' => 'text'
        ]);
    }

	public function company()
	{
		abort(501, 'Server: Company Not implemented');
		//return $this->belongsTo(Company::class);
	}

	public function serverMetrics()
	{
		abort(501, 'Server: Server Metrics Not implemented');
		//return $this->hasMany(ServerMetric::class);
	}

}

//Generated 01-11-2023 11:27:41
