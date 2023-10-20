<?php

namespace App\Models;

use App\Traits\HasSensesTable;
use App\Traits\SensesModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Senses\Builder\BlockGroup as BuilderBlockGroup;
use Spatie\QueryBuilder\AllowedFilter;

class BlockGroup extends BuilderBlockGroup
{
    use HasSensesTable, SensesModel;

    static $disableHiddenFields = true;

    protected $fillable = [
        'display_name', 'name', 'description', 'block_group_types'
    ];

    protected $casts = [
        'block_group_types' => 'array'
    ];

    public function allowedSorts()
    {
        return ["uuid", "app_id",'id', 'name', 'display_name', 'description', 'builderCategory.title'];
    }

    public function allowedEmbeds()
    {
        return [];
    }

    public function allowedFields()
    {
        return ["uuid", "app_id",'id', 'name', 'display_name', 'description', 'builderCategory.title'];
    }

    public function builderCategory(): BelongsTo
    {
        return $this->belongsTo(BuilderCategory::class);
    }

    public function allowedFilters(): array
    {
        return $this->defineFilters([
            'id' => 'integer',
            'name' => 'text',
            'display_name' => 'text',
            'description' => 'text',
            AllowedFilter::scope('type-includes', 'whereType'),
        ]);
    }

    public function scopeWhereType($query, $type)
    {
        $query->whereRaw("block_group_types::jsonb ??| array['" . $type . "']");
    }
}
