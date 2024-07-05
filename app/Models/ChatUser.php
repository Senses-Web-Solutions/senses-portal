<?php

namespace App\Models;

use App\Traits\SensesModel;
use App\Traits\HasActionLogs;

use App\Casts\DateTime;
use Illuminate\Database\Eloquent\Model;

class ChatUser extends Model
{
    use SensesModel, HasActionLogs;

    protected $fillable = [
        'first_name',
        'last_name',
        'full_name',
        'email',
        'external_id',
        'system'
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
            $q->orWhere($table . '.name', 'ilike', '%' . $search . '%');
        });
    }

    public function allowedSorts()
    {
        return ['id', 'first_name', 'last_name', 'full_name', 'email', 'external_id', 'system', 'company_id'];
    }

    public function allowedEmbeds()
    {
        return [];
    }

    public function allowedFields()
    {
        return ['id', 'first_name', 'last_name', 'full_name', 'email', 'external_id', 'system', 'company_id', 'average_review', 'total_reviews'];
    }

    public function allowedFilters()
    {
        return $this->defineFilters([
            'id' => 'integer',
            'company_id' => 'integer',
            'system' => 'string',
            'first_name' => 'string',
            'last_name' => 'string',
            'full_name' => 'string',
            'email' => 'string',
            'external_id' => 'string',
        ]);
    }

    public function chats()
    {
        return $this->belongsToMany(Chat::class, 'chat_users');
    }

    public function messages()
    {
        return $this->morphMany(Message::class, 'author');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function chatReviews()
    {
        return $this->hasMany(ChatReview::class);
    }

    public function getAverageReviewAttribute()
    {
        return $this->chatReviews()->avg('overall');
    }

    public function getTotalReviewsAttribute()
    {
        return $this->chatReviews()->count();
    }
}

//Generated 01-11-2023 11:22:36
