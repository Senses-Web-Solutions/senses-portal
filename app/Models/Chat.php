<?php

namespace App\Models;

use App\Casts\Point;
use App\Traits\HasPostgis;
use App\Traits\SensesModel;
use App\Traits\HasActionLogs;

use App\Casts\DateTime;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use SensesModel, HasActionLogs, HasPostgis;

    protected $fillable = [
        'system',
        'meta',
        'country_code',
        'ip',
        'language',
        'timezone',
        'device_resolution',
        'tab_resolution',
        'browser',
        'browser_version',
        'os',
        'os_version',
        'device',
    ];

    protected $casts = [
        'locked_at' => DateTime::class,
        'created_at' => DateTime::class,
        'updated_at' => DateTime::class,
        'deleted_at' => DateTime::class,
        'hidden_at' => DateTime::class,
        'meta' => 'json',
        'geom' => Point::class,
    ];

    protected $excludedActionLogs = ['created', 'updated'];

    public function scopeTableSearch($query, $search)
    {
        $table = $this->getTable();
        $query->where(function ($q) use (&$search, $table) {
            $q->where($table . '.id', 'like', '%' . $search . '%');
            $q->orWhere($table . '.name', 'ilike', '%' . $search . '%');
        });
    }

    public function allowedSorts()
    {
        return ['id', 'company_id', 'system', 'meta', 'completed_at', 'status_id', 'country_code'];
    }

    public function allowedEmbeds()
    {
        return [];
    }

    public function allowedFields()
    {
        return ['id', 'company_id', 'system', 'meta', 'created_at', 'completed_at', 'status_id', 'country_code'];
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
            'country_code' => 'string',
        ]);
    }

    public function agents()
    {
        return $this->belongsToMany(User::class, 'chat_agents');
    }

    public function historicalAgents()
    {
        return $this->belongsToMany(User::class, 'chat_historical_agents');
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

    public function actionLogs()
    {
        return $this->morphMany(ActionLog::class, 'loggable');
    }

    public function chatUser()
    {
        return $this->belongsTo(ChatUser::class);
    }

    public function getLastMessageAttribute()
    {
        return Message::where('chat_id', $this->id)->latest()->with('author')->first(['id', 'chat_id', 'content', 'sent_at']);
    }

    public function getUnreadMessagesAttribute()
    {
        return Message::where('chat_id', $this->id)->where('read_at', null)->with('author')->get(['id', 'chat_id', 'content', 'sent_at']);
    }

    public function getUnreadMessagesCountAttribute()
    {
        // For counting, selecting specific columns is unnecessary and ignored by the query builder.
        return Message::where('chat_id', $this->id)->where('read_at', null)->where('from_agent', false)->count();
    }

    public function toArray()
    {
        $array = parent::toArray();

        // Convert messages to an object keyed by message id
        $array['messages'] = (object) $this->messages()->with(['author:id,full_name'])->get([
            'id', 'chat_id', 'from_agent', 'content', 'sent_at', 'read_at', 'read_by', 'author_type', 'author_id'
        ])->keyBy('id')->all();

        return $array;
    }
}

//Generated 01-11-2023 11:22:36
