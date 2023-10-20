<?php

namespace App\Models;

use Illuminate\Support\Facades\URL;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Spatie\QueryBuilder\AllowedFilter;

class Notification extends DatabaseNotification
{
    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'url' => 'array',
    ];

    protected $appends = ['object'];

    public function getObjectAttribute()
    {
        return $this->getMorphClass();
    }

    public function allowedFilters(): array
    {
        return [
            AllowedFilter::scope('unread')
        ];
    }

    public function allowedFields(): array
    {
        return [
            'id',
            'read_at',
            'data',
            'created_at',
            'url',
            'updated_at',
            'notifiable_type'
        ];
    }

    public function allowedSorts()
    {
        return ['created_at', 'read_at'];
    }

    public function defaultSort(): string
    {
        return '-created_at';
    }

    public function getUrlAttribute($value)
    {
        $value = $this->castAttribute('url', $value);
        if ($value) {
            if ($value['signed']) {
                $value['href'] = URL::temporarySignedRoute($value['href']['route'], now()->addMinutes(30), $value['href']['params'] ?? []);
                return $value;
            }
            return $value;
        }
    }
}
