<?php

namespace App\Actions\ActionLogs;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueueableAction\QueueableAction;
use App\Actions\ActionLogs\HasRunModelSpecificAction;

class LogCreatedActionLog
{
    use QueueableAction {
        tags as traitTags;
    }

    public $queue = 'low';

    public function tags() {
        return [...$this->traitTags(), 'action_logs'];
    }

    public function execute(Model $model, Carbon $logDate = null, User $user = null)
    {
        rescue(function () use ($model, $logDate, $user) {
            $actionLog = app(HasRunModelSpecificAction::class)->execute('Created', $model, $logDate, $user);

            if($actionLog) {
                return $actionLog;
            }

            return app(CreateActionLog::class)->execute($model, 'created', [], $logDate, $user);
        });
    }
}
