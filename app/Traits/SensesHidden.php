<?php

namespace App\Traits;

use App\Models\User;
use App\Scopes\HiddenScope;

/**
 * @method static \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder withHidden(bool $withHidden = true)
 * @method static \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder onlyHidden()
 * @method static \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder withoutHidden()
 */
trait SensesHidden
{
    /**
     * Boot the hidden trait for a model.
     *
     * @return void
     */
    public static function bootSensesHidden()
    {
        $hiddenFields = true;

        if (property_exists(static::class, 'disableHiddenFields')) {
            $hiddenFields = !static::$disableHiddenFields;
        }

        if ($hiddenFields) {
            static::addGlobalScope(new HiddenScope);
        }
    }

    /**
     * Initialize the hidden trait for an instance.
     *
     * @return void
     */
    public function initializeSensesHidden()
    {
        if (! isset($this->casts['hidden_at'])) {
            $this->casts['hidden_at'] = 'datetime';
        }
    }


    /**
     * Determine if the model instance has been soft-deleted.
     *
     * @return bool
     */
    public function hidden()
    {
        return ! is_null($this->{'hidden_at'});
    }

    /**
     * Get the fully qualified "deleted at" column.
     *
     * @return string
     */
    public function getQualifiedHiddenAtColumn()
    {
        return $this->qualifyColumn('hidden_at');
    }

    public function hider()
    {
        return $this->belongsTo('App\Models\User', 'hidden_by');
    }

    public function hide(User $user = null, bool $save = true, bool $silent = false, bool $force = false)
    {
        if (!$force && $this->hidden_at) {
            return;
        }

        if (!$silent) {
            event('eloquent.hiding: ' . get_class($this), $this);
        }

        $user = $user ?? getCurrentUser();
        $user = $user ?? getSensesSystemUser();

        $this->hider()->associate($user);
        $this->hidden_at = now();

        if ($save) {
            $this->saveQuietlyWithRevisions();
        }

        if (!$silent) {
            event('eloquent.hidden: ' . get_class($this), $this);
        }
    }

    public function unhide(bool $save = true, bool $silent = false)
    {
        if (!$this->hidden_at) {
            return;
        }

        if (!$silent) {
            event('eloquent.unhiding: ' . get_class($this), $this);
        }

        $this->hidden_by = null;
        $this->hidden_at = null;

        if ($save) {
            $this->saveQuietlyWithRevisions();
        }

        if (!$silent) {
            event('eloquent.unhidden: ' . get_class($this), $this);
        }
    }
}
