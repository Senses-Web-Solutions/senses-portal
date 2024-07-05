<?php

namespace App\Models;

use App\Traits\SensesModel;
use App\Traits\HasActionLogs;

use App\Casts\DateTime;
use Illuminate\Database\Eloquent\Model;

class ChatReview extends Model
{
    use SensesModel, HasActionLogs;

    protected $fillable = [
        'knowledge',
        'friendliness',
        'responsiveness',
        'overall',
        'resolved',
        'comment'
    ];

    protected $casts = [
        'locked_at' => DateTime::class,
        'created_at' => DateTime::class,
        'updated_at' => DateTime::class,
        'deleted_at' => DateTime::class,
        'hidden_at' => DateTime::class,
    ];

    protected $excludedActionLogs = ['created', 'updated'];

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
        return ['id', 'chat_id', 'knowledge', 'friendliness', 'responsiveness', 'overall', 'resolved', 'comment'];
    }

    public function allowedEmbeds()
    {
        return [];
    }

    public function allowedFields()
    {
        return ['id', 'chat_id', 'knowledge', 'friendliness', 'responsiveness', 'overall', 'resolved', 'comment'];
    }

    public function allowedFilters()
    {
        return $this->defineFilters([
            'id' => 'integer',
            'chat_id' => 'integer',
            'knowledge' => 'integer',
            'friendliness' => 'integer',
            'responsiveness' => 'integer',
            'resolved' => 'boolean',
            'overall' => 'integer',
        ]);
    }

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function chatUser()
    {
        return $this->belongsTo(ChatUser::class);
    }
}

//Generated 01-11-2023 11:22:36
