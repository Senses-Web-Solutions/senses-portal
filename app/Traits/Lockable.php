<?php

namespace App\Traits;

use App\Casts\DateTime;
use App\Models\User;
use App\Enums\LockType;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

trait Lockable
{
    public function initializeLockable()
    {
        $this->addObservableEvents([
            'locked',
            'unlocked',
            'locking',
            'unlocking'
        ]);

        $this->mergeCasts([
            'locked_at' => DateTime::class,
            'lock_type' => LockType::class,
        ]);
    }

    public function getLockedAttribute($value)
    {
        return isset($this->attributes['locked_at']);
    }

    public function locker()
    {
        return $this->belongsTo('App\Models\User', 'locked_by')->withHidden();;
    }

    public function lock(LockType $lockType = null, User $user = null, bool $save = true, bool $silent = false, bool $force = false)
    {
        if (!$force && $this->locked_at) {
            return;
        }

        if(!$silent) {
            event('eloquent.locking: ' . get_class($this), $this);
        }
        if (!$lockType) {
            $lockType = LockType::CORE;
        }

        $user = $user ?? getCurrentUser();
        $user = $user ?? getSensesSystemUser();
        $this->locker()->associate($user);
        $this->locked_at = now();
        $this->lock_type = $lockType;

        //doesn't quite equate to save()
        if($save) {
            $this->saveQuietlyWithRevisions();
        }
        if(!$silent) {
            event('eloquent.locked: ' . get_class($this), $this);
        }
    }

    public function unlock(bool $save = true, bool $silent = false)
    {
        if (!$this->locked_at) {
            return;
        }
        if(!$silent) {
            event('eloquent.unlocking: ' . get_class($this), $this);
        }
        $this->locked_by = null;
        $this->locked_at = null;
        $this->lock_type = null;

        if($save) {
            $this->saveQuietlyWithRevisions();
        }

        if(!$silent) {
            event('eloquent.unlocked: ' . get_class($this), $this);
        }
    }


    public function scopeUnlocked(Builder $query)
    {
        return $query->whereNull('locked_at');
    }
}
