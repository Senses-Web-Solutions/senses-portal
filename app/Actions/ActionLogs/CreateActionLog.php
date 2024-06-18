<?php

namespace App\Actions\ActionLogs;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use App\Actions\Tasks\FindRelatedTaskID;
use Spatie\QueueableAction\QueueableAction;

class CreateActionLog
{
    use QueueableAction;

    public function execute(Model $model, String $type, array $data, Carbon $logDate = null, User $user = null): Model
    {
        $actionLog = app($model->getActionLogModel());

        if (isset($logDate)) {
            $actionLog->logged_at = $logDate;
        }
        else {
            $actionLog->logged_at = now();
        }

        if(!$user){
            $user = getCurrentUserOrSystemUser();
        }

        $actionLog->type = $type;
        $actionLog->data = $data;
        $actionLog->user()->associate($user);
        $actionLog->loggable()->associate($model);


        $actionLog->save();
        return $actionLog;
    }
}
