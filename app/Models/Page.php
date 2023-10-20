<?php

namespace App\Models;

use App\Casts\Date;

use App\Casts\Money;
use App\Enums\Format;
use App\Casts\DateTime;
use App\Enums\PageType;
use App\Traits\SensesModel;
use Senses\Builder\HasBuilds;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use SensesModel, HasBuilds;


    protected $fillable = [
		'app_id',
        'title',
        'slug',
        'excerpt',
        'type',
        'status_id',
        'meta_title',
        'meta_description',
        'featured',
        'show_last_updated',
];

    protected $casts = [
		'locked_at' => DateTime::class,
		'created_at' => DateTime::class,
		'updated_at' => DateTime::class,
		'deleted_at' => DateTime::class,
		'hidden_at' => DateTime::class,
        'type' => PageType::class,
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
        return ['id', 'title', 'slug', 'status', 'featured'];
    }

    public function allowedEmbeds()
    {
        return [];
    }

    public function allowedFields()
    {
        return ['id', 'title', 'slug', 'status', 'featured'];
    }

    public function allowedFilters() {
        return $this->defineFilters([
            'id' => 'integer',
            'title' => 'text',
			'slug' => 'text',
			'status' => 'text',
			'featured' => 'text'
        ]);
    }

    public function targetAudiences()
	{
		return $this->morphToMany(TargetAudience::class, 'target_audienceable')->withTimestamps();
	}

    public function status() {
        return $this->belongsTo(Status::class);
    }

    public function getContentAttribute(): array
    {
        return $this->belongsToBuild()->getResults()->content ?? [];
    }

	public function keywords()
	{
		return $this->morphToMany(Keyword::class, 'keywordable');
	}

}

//Generated 10-10-2023 14:43:35
