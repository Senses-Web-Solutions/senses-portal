<?php

namespace App\Models;

use App\Traits\HasTitleSlug;
use App\Traits\SensesModel;

use Illuminate\Database\Eloquent\Model;
use App\Enums\Format;
use App\Casts\DateTime;
use App\Casts\Money;
use App\Casts\Date;

class Company extends Model
{
    use SensesModel, HasTitleSlug;


    protected $fillable = [
		'app_id',
		'title',
		'slug'
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
        return ['id', 'title', 'slug'];
    }

    public function allowedEmbeds()
    {
        return ['users', 'servers'];
    }

    public function allowedFields()
    {
        return ['id', 'title', 'slug'];
    }

    public function allowedFilters() {
        return $this->defineFilters([
            'id' => 'integer',
            'title' => 'text',
			'slug' => 'text'
        ]);
    }

	public function servers()
	{
		// abort(501, 'Company: Servers Not implemented');
		return $this->hasMany(Server::class);
	}

	public function users()
	{
		// abort(501, 'Company: Users Not implemented');
		return $this->hasMany(User::class);
	}

}

//Generated 27-10-2023 10:55:44
