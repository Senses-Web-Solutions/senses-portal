<?php

namespace App\Models;

use App\Traits\SensesModel;

use Illuminate\Database\Eloquent\Model;
use App\Casts\DateTime;

class Message extends Model
{
    use SensesModel;


    protected $fillable = [
        'content',
        'meta',
        'sent_at'
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
        return ['id', 'chat_id', 'from_agent', 'content', 'status_id', 'meta', 'sent_at'];
    }

    public function allowedEmbeds()
    {
        return [];
    }

    public function allowedFields()
    {
        return ['id', 'chat_id', 'from_agent', 'content', 'status_id', 'meta', 'sent_at'];
    }

    public function allowedFilters()
    {
        return $this->defineFilters([
            'id' => 'integer',
            'chat_id' => 'integer',
            'content' => 'string',
            'status_id' => 'integer',
            'meta' => 'string',
            'completed_at' => 'datetime',
        ]);
    }

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }
}

//Generated 01-11-2023 11:22:36
