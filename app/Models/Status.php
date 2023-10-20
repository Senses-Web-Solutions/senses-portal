<?php

namespace App\Models;

use App\Traits\HasTitleSlug;
use App\Traits\SensesModel;

use Illuminate\Database\Eloquent\Model;
use App\Enums\Format;
use App\Casts\DateTime;
use App\Casts\Money;
use App\Casts\Date;
use Spatie\QueryBuilder\AllowedFilter;

class Status extends Model
{
    use SensesModel, HasTitleSlug;


    protected $fillable = [
		'app_id',
		'title',
		'slug',
		'colour'
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
        return ['id', 'title', 'slug', 'colour'];
    }

    public function allowedEmbeds()
    {
        return ['services', 'reviews', 'venues', 'organisations', 'venues', 'statusGroup'];
    }

    public function allowedFields()
    {
        return ['id', 'title', 'slug', 'statusGroup.title', 'colour'];
    }

    public function allowedFilters() {
        return $this->defineFilters([
            'id' => 'integer',
            'title' => 'text',
			'slug' => 'text',
			// 'statusGroups.title' => 'text',
			// 'statusGroups.slug' => 'text',
			AllowedFilter::scope('status-group-slug', 'whereStatusGroupSlug'),

			'colour' => 'text'
        ]);
    }

	public function organisations()
	{
		abort(501, 'Status: Organisations Not implemented');
		//return $this->hasMany(Organisation::class);
	}

	public function reviews()
	{
		abort(501, 'Status: Reviews Not implemented');
		//return $this->hasMany(Review::class);
	}

	public function services()
	{
		abort(501, 'Status: Services Not implemented');
		//return $this->hasMany(Service::class);
	}

	public function statusGroups()
	{
		return $this->belongsToMany(StatusGroup::class)->withTimestamps();
	}

	public function venues()
	{
		abort(501, 'Status: Venues Not implemented');
		//return $this->hasMany(Venue::class);
	}

	public function scopeWhereStatusGroupSlug($query, $slug)
	{
		return $query->whereHas('statusGroups', function ($query) use ($slug) {
			$query->where('slug', $slug);
		});
	}
}

//Generated 09-10-2023 12:35:29
