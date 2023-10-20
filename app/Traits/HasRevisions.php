<?php

namespace App\Traits;

use App\Models\Revision;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

trait HasRevisions
{
    protected static function bootHasRevisions()
    {
        static::updating(function (Model $model) {
            if($model->revisionsEnabled !== false) {
                static::recordRevision($model);
            }
        });
    }

    public function getRevisionModel()
    {
        return $this->revisionModel ?? Revision::class;
    }

    public function revisions()
    {
        return $this->morphMany($this->getRevisionModel(), 'revisionable');
    }

    public function latestRevision()
    {
        return $this->revisions()->latest();
    }

    public static function recordRevision(Model $model) : Model|null {
        $after = $model->getDirty();

        // dd($model->getDirty(), $model->getChanges());

        //disregard remember_token
        if(isset($after['remember_token']) && count($after) === 1) {
            return null;
        }

        foreach ($after as $key => $change) {
            $after[$key] = $model->transformModelValue($key, $change);
        }

        $before = array_intersect_key($model->fresh()->toArray(), $after);

        if(empty($before) && empty($after)) {
            return null;
        }

        $revision = $model->getRevisionModel()::make(['before' => $before, 'after' => $after]);
        $revision->revisionable()->associate($model);
        $revision->save();

        return $revision;
    }

    public function saveQuietlyWithRevisions() {
        if ($this->exists) {
            if ($this->fireModelEvent('updating') === false) {
                return false;
            }
            return $this->saveQuietly();
        }

        return $this->saveQuietly();
    }
}
