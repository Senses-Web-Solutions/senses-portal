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
		'cpu_use',
		'cpu_us',
		'cpu_sy',
		'cpu_ni',
		'cpu_id',
		'cpu_wa',
		'cpu_hi',
		'cpu_si',
		'cpu_st',
		'load_1',
		'load_5',
		'load_15',
		'ram_total',
		'ram_free',
		'ram_buffer',
		'ram_cache',
		'ram_used',
		'swap_total',
		'swap_free',
		'swap_used',
		'swap_cache',
		'disk_total',
		'disk_free',
		'disk_used',
		'disk_read',
		'disk_write'
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
        return ['id', 'timestamp', 'uptime', 'cpu_use', 'cpu_us', 'cpu_sy', 'cpu_ni', 'cpu_id', 'cpu_wa', 'cpu_hi', 'cpu_si', 'cpu_st', 'load_1', 'load_5', 'load_15', 'ram_total', 'ram_free', 'ram_buffer', 'ram_cache', 'ram_used', 'swap_total', 'swap_free', 'swap_used', 'swap_cache', 'disk_total', 'disk_free', 'disk_used', 'disk_read', 'disk_write'];
    }

    public function allowedEmbeds()
    {
        return [];
    }

    public function allowedFields()
    {
        return ['id', 'timestamp', 'uptime', 'cpu_use', 'cpu_us', 'cpu_sy', 'cpu_ni', 'cpu_id', 'cpu_wa', 'cpu_hi', 'cpu_si', 'cpu_st', 'load_1', 'load_5', 'load_15', 'ram_total', 'ram_free', 'ram_buffer', 'ram_cache', 'ram_used', 'swap_total', 'swap_free', 'swap_used', 'swap_cache', 'disk_total', 'disk_free', 'disk_used', 'disk_read', 'disk_write'];
    }

    public function allowedFilters() {
        return $this->defineFilters([
            'id' => 'integer',
            'timestamp' => 'text',
			'uptime' => 'text',
			'cpu_use' => 'text',
			'cpu_us' => 'text',
			'cpu_sy' => 'text',
			'cpu_ni' => 'text',
			'cpu_id' => 'text',
			'cpu_wa' => 'text',
			'cpu_hi' => 'text',
			'cpu_si' => 'text',
			'cpu_st' => 'text',
			'load_1' => 'text',
			'load_5' => 'text',
			'load_15' => 'text',
			'ram_total' => 'text',
			'ram_free' => 'text',
			'ram_buffer' => 'text',
			'ram_cache' => 'text',
			'ram_used' => 'text',
			'swap_total' => 'text',
			'swap_free' => 'text',
			'swap_used' => 'text',
			'swap_cache' => 'text',
			'disk_total' => 'text',
			'disk_free' => 'text',
			'disk_used' => 'text',
			'disk_read' => 'text',
			'disk_write' => 'text'
        ]);
    }

	public function company()
	{
		// abort(501, 'Server Metric: Company Not implemented');
		return $this->belongsTo(Company::class);
	}

	public function server()
	{
		// abort(501, 'Server Metric: Server Not implemented');
		return $this->belongsTo(Server::class);
	}

}

//Generated 01-11-2023 11:22:36
