<?php

namespace App\Actions\ActionLogs;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueueableAction\QueueableAction;
use App\Actions\ActionLogs\HasRunModelSpecificAction;

class LogLockedActionLog
{
    use QueueableAction;

    public $queue = 'low';
    
    public function execute(Model $model, Carbon $logDate = null, User $user = null)
    {
        $actionLog = app(HasRunModelSpecificAction::class)->execute('Locked', $model, $logDate, $user);

        if($actionLog) {
            return $actionLog;
        }

        return app(CreateActionLog::class)->execute($model, 'locked', [], $logDate, $user);
    }
}
