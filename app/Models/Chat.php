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
        return ['id', 'user_id', 'company_id', 'system', 'meta', 'completed_at', 'status_id'];
    }

    public function allowedEmbeds()
    {
        return [];
    }

    public function allowedFields()
    {
        return ['id', 'user_id', 'company_id', 'system', 'meta', 'completed_at', 'status_id'];
    }

    public function allowedFilters()
    {
        return $this->defineFilters([
            'id' => 'integer',
            'user_id' => 'integer',
            'company_id' => 'integer',
            'system' => 'string',
            'meta' => 'string',
            'completed_at' => 'datetime',
            'status_id' => 'integer',
        ]);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
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
        return Message::where('chat_id', $this->id)->where('read_at', null)->count();
    }
}

//Generated 01-11-2023 11:22:36
