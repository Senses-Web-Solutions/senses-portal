<?php

namespace App\Traits;

use Carbon\Carbon;
use App\Models\ActionLog;
use App\Actions\ActionLogs\LogLockedActionLog;
use App\Actions\ActionLogs\LogCreatedActionLog;
use App\Actions\ActionLogs\LogDeletedActionLog;
use App\Actions\ActionLogs\LogUpdatedActionLog;
use App\Actions\ActionLogs\LogRestoredActionLog;
use App\Actions\ActionLogs\LogUnlockedActionLog;
use App\Actions\ActionLogs\LogForceDeletedActionLog;

trait HasActionLogs
{
    public $disabledActionLogs = false;

    protected static function bootHasActionLogs()
    {
        static::created(function ($model) {
            if($model->actionLogsEnabled !== false && $model->isEventActionLoggable('created', $model)) {
                app(LogCreatedActionLog::class)->onQueue('low')->execute($model->unsetRelations(), Carbon::parse($model->created_at), $model->creator);
            }
        });

        static::updated(function ($model) {
            if($model->actionLogsEnabled !== false && $model->isEventActionLoggable('updated', $model)) {
                if(isset($model->getChanges()['remember_token']) && count($model->getChanges()) === 1) {
                    return; //ignore logging out, it's not a real change and its updated_at isn't carbon?
                }
                app(LogUpdatedActionLog::class)->onQueue('low')->execute($model->unsetRelations(), Carbon::parse($model->updated_at), $model->updater);
            }
        });

        //register these as they are dynamically initialized.
        static::registerModelEvent('locked', function ($model) {
            if($model->actionLogsEnabled !== false && $model->isEventActionLoggable('locked', $model)) {
                app(LogLockedActionLog::class)->onQueue('low')->execute($model->unsetRelations(), Carbon::parse($model->locked_at), getCurrentUser());
            }
        });

        static::registerModelEvent('unlocked', function ($model) {
            if($model->actionLogsEnabled !== false && $model->isEventActionLoggable('unlocked', $model)) {
                app(LogUnlockedActionLog::class)->onQueue('low')->execute($model->unsetRelations(), now(), getCurrentUser());
            }
        });

        static::deleted(function ($model) {
            if($model->actionLogsEnabled !== false && $model->isEventActionLoggable('deleted', $model)) {
                app(LogDeletedActionLog::class)->onQueue('low')->execute($model->unsetRelations(), Carbon::parse($model->deleted_at), $model->deleter);
            }
        });

        static::forceDeleted(function ($model) {
            if($model->actionLogsEnabled !== false && $model->isEventActionLoggable('forceDeleted', $model)) {
                app(LogForceDeletedActionLog::class)->onQueue('low')->execute($model->unsetRelations(), now(), getCurrentUser());
            }
        });

        static::restored(function ($model) {
            if($model->actionLogsEnabled !== false && $model->isEventActionLoggable('restored', $model)) {
                app(LogRestoredActionLog::class)->onQueue('low')->execute($model, now(), getCurrentUser());
            }
        });

    }

    public function getActionLogModel()
    {
        return $this->actionLogModel ?? ActionLog::class;
    }

    public function isEventActionLoggable($event, $model) {
        if($model->disabledActionLogs) {
            return false;
        }

        if(isset($this->excludedActionLogs) && in_array($event, $this->excludedActionLogs)) {
            return false;
        }

        if(!isset($this->includedActionLogs)) {
            return true;
        }

        if(in_array($event, $this->includedActionLogs)) {
            return true;
        }

        return false;
    }

    public function disableActionLogs()
    {
        $this->disabledActionLogs = true;
    }

    public function enableActionLogs()
    {
        $this->disabledActionLogs = false;
    }

    public function saveWithoutActionLogs()
    {
        $this->disableActionLogs();
        $this->save();
        $this->enableActionLogs();
        return $this;
    }
}
