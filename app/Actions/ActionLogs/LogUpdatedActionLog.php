<?php

namespace App\Actions\ActionLogs;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueueableAction\QueueableAction;
use App\Actions\ActionLogs\HasRunModelSpecificAction;

class LogUpdatedActionLog
{
    use QueueableAction;

    public $queue = 'low';

    public function execute(Model $model, Carbon $logDate = null, User $user = null)
    {
        rescue(function () use ($model, $logDate, $user) {
            $actionLog = app(HasRunModelSpecificAction::class)->execute('Updated', $model, $logDate, $user);
    
            if($actionLog) {
                return $actionLog;
            }
    
            return app(CreateActionLog::class)->execute($model, 'updated', [], $logDate, $user);
        });
    }
}
