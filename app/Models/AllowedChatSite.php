<?php

namespace App\Models;

use App\Traits\SensesModel;

use Illuminate\Database\Eloquent\Model;
use App\Casts\DateTime;

class AllowedChatSite extends Model
{
    use SensesModel;

    protected $fillable = [
        'title',
        'url',
    ];

    protected $casts = [
        'locked_at' => DateTime::class,
        'created_at' => DateTime::class,
        'updated_at' => DateTime::class,
        'deleted_at' => DateTime::class,
        'hidden_at' => DateTime::class,
        'meta' => 'json'
    ];

    public function scopeTableSearch($query, $search)
    {
        $table = $this->getTable();
        $query->where(function ($q) use (&$search, $table) {
            $q->where($table . '.id', 'like', '%' . $search . '%');
            $q->orWhere($table . '.title', 'ilike', '%' . $search . '%');
        });
    }

    public function allowedSorts()
    {
        return ['id', 'company_id', 'title', 'url'];
    }

    public function allowedEmbeds()
    {
        return [];
    }

    public function allowedFields()
    {
        return ['id', 'company_id', 'title', 'url'];
    }

    public function allowedFilters()
    {
        return $this->defineFilters([
            'id' => 'integer',
            'company_id' => 'integer',
            'title' => 'string',
            'url' => 'string',
        ]);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}

//Generated 01-11-2023 11:22:36
