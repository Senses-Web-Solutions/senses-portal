<?php

namespace App\Models;

use App\Traits\SensesModel;

use Illuminate\Database\Eloquent\Model;
use App\Casts\DateTime;
use App\Traits\HasFiles;

class CannedMessage extends Model
{
    use SensesModel, HasFiles;

    protected $fillable = [
        'system',
        'title',
        'content',
        'shortcut'
    ];

    protected $casts = [
        'locked_at' => DateTime::class,
        'created_at' => DateTime::class,
        'updated_at' => DateTime::class,
        'deleted_at' => DateTime::class,
        'hidden_at' => DateTime::class,
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
        return ['id', 'user_id', 'system', 'title', 'content', 'active', 'shortcut'];
    }

    public function allowedFields()
    {
        return ['id', 'user_id', 'system', 'title', 'content', 'active', 'shortcut'];
    }

    public function allowedFilters()
    {
        return $this->defineFilters([
            'id' => 'integer',
            'user_id' => 'integer',
            'title' => 'text',
            'content' => 'text',
            'shortcut' => 'text',
        ]);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

//Generated 01-11-2023 11:22:36
