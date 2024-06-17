<?php

namespace App\Models;


use App\Traits\Routable;
use App\Traits\Notifiable;
use App\Traits\HasTextColour;
use App\Traits\SensesModel;
use Laravel\Passport\HasApiTokens;
use App\Traits\WhereDateRangeBetween;
use Illuminate\Database\Eloquent\Builder;
use App\Notifications\RegistrationRequired;
use Illuminate\Auth\Notifications\ResetPassword;
use Silber\Bouncer\Database\HasRolesAndAbilities;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use SensesModel, HasApiTokens, Notifiable, WhereDateRangeBetween, HasRolesAndAbilities, HasTextColour, Routable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'email',
        'password',
        'first_name',
        'last_name',
        'full_name',
    ];

    protected $observables = ['rolesUpdated'];

    protected $appends = [
        'initials',
        'object',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'email' => $this->email,
        ];
    }

    public function sendPasswordResetNotification($token)
    {
        if (!$this->password && $this->userType->slug == 'public') {
            $this->notify(new RegistrationRequired($token));
        } else {
            $this->notify(new ResetPassword($token));
        }
    }

    public function userSettings()
    {
        return $this->hasMany(UserSetting::class);
    }

    public function getInitialsAttribute(): ?string
    {
        if (!isset($this->attributes['first_name']) || !isset($this->attributes['last_name'])) {
            return null;
        }
        return strtoupper(substr($this->attributes['first_name'], 0, 1) . substr($this->attributes['last_name'], 0, 1));
    }

    public function setEmailAttribute($value)
    {
        if($value) {
            $this->attributes['email'] = strtolower($value);
        }

        $this->attributes['email'] = $value;
    }

    public function receivesBroadcastNotificationsOn(): string
    {
        return 'users.' . $this->id . '.notifications';
    }

    public function allowedFilters()
    {
        return $this->defineFilters([
            "id" => "integer",
            "first_name" => "text",
            "last_name" => "text",
            "full_name" => "text",
            "email" => "text",
        ]);
    }

    public function allowedEmbeds()
    {
        return ['roles'];
    }

    public function allowedFields()
    {
        return [
            'id',
            'email',
            'first_name',
            'last_name',
            'full_name',
            'roles.title',
            'roles.id',
            'hidden_at',
            'deleted_at',
            'hider.full_name',

            'email_verified_at',
        ];
    }

    public function allowedSorts()
    {
        return ['id', 'first_name', 'last_name', 'full_name', 'email'];
    }

    public function scopeTableSearch(Builder $query, $search)
    {
        $table = $this->getTable();
        $query->where(function ($q) use (&$search, $table) {
            $q->where($table . '.id', 'like', '%' . $search . '%');
            $q->orWhere($table . '.full_name', 'ilike', '%' . $search . '%');
        });
    }

    public function chats()
    {
        return $this->belongsToMany(Chat::class, 'chat_agents');
    }

    public function emitRolesUpdated(array $before = [], array $after = [])
    {
        event('eloquent.rolesUpdated: ' . get_class($this), [$this, $before, $after]);
    }

}
