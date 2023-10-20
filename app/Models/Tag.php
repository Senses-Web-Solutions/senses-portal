<?php

namespace App\Models;

use App\Traits\HasTitleSlug;
use App\Traits\SensesModel;

use Illuminate\Database\Eloquent\Model;
use App\Enums\Format;
use App\Casts\DateTime;
use App\Casts\Money;
use App\Casts\Date;

class Tag extends Model
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
        return ['services', 'events', 'venues', 'files', 'links', 'tagGroup'];
    }

    public function allowedFields()
    {
        return ['id', 'title', 'slug', 'tagGroup.title', 'colour'];
    }

    public function allowedFilters() {
        return $this->defineFilters([
            'id' => 'integer',
            'title' => 'text',
			'slug' => 'text',
			'tagGroup.title' => 'text',
			'colour' => 'text'
        ]);
    }

	public function tagGroups()
	{
		return $this->belongsToMany(TagGroup::class)->withTimestamps();
	}

	public function events()
	{
		abort(501, 'Tag: Events Not implemented');
		//return $this->morphedByMany(Event::class, 'taggable');
	}

	public function files()
	{
		abort(501, 'Tag: Files Not implemented');
		//return $this->morphedByMany(File::class, 'taggable');
	}

	public function links()
	{
		return $this->morphedByMany(Link::class, 'taggable')->withTimestamps();
	}

	public function services()
	{
		abort(501, 'Tag: Services Not implemented');
		//return $this->morphedByMany(Service::class, 'taggable');
	}

	public function venues()
	{
		abort(501, 'Tag: Venues Not implemented');
		//return $this->morphedByMany(Venue::class, 'taggable');
	}

}

//Generated 09-10-2023 10:18:19
