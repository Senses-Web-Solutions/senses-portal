<?php

namespace App\Models;

use App\Traits\SensesModel;

use Illuminate\Database\Eloquent\Model;
use App\Casts\DateTime;

class Chat extends Model
{
    use SensesModel;


    protected $fillable = [
        'system',
        'name',
        'meta',
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
        return ['id', 'company_id', 'system', 'meta', 'completed_at', 'status_id'];
    }

    public function allowedEmbeds()
    {
        return [];
    }

    public function allowedFields()
    {
        return ['id', 'company_id', 'system', 'meta', 'completed_at', 'status_id'];
    }

    public function allowedFilters()
    {
        return $this->defineFilters([
            'id' => 'integer',
            'company_id' => 'integer',
            'system' => 'string',
            'meta' => 'string',
            'completed_at' => 'datetime',
            'status_id' => 'integer',
        ]);
    }

    public function agents()
    {
        return $this->belongsToMany(User::class, 'chat_agents');
    }

    public function invitedAgents()
    {
        return $this->belongsToMany(User::class, 'chat_invited_agents');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function getLastMessageAttribute()
    {
        return Message::where('chat_id', $this->id)->latest()->first();
    }

    public function getUnreadMessagesAttribute()
    {
        return Message::where('chat_id', $this->id)->where('read_at', null)->get();
    }

    public function getUnreadMessagesCountAttribute()
    {
        return Message::where('chat_id', $this->id)->where('read_at', null)->where('from_agent', false)->count();
    }

    public function toArray()
    {
        $array = parent::toArray();

        // Convert messages to an object keyed by message id
        $array['messages'] = (object) $this->messages->keyBy('id')->all();

        return $array;
    }
}

//Generated 01-11-2023 11:22:36
