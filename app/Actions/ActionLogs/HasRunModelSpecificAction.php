<?php

namespace App\Actions\ActionLogs;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueueableAction\QueueableAction;

class HasRunModelSpecificAction
{
    use QueueableAction;

    public function execute(String $event, Model $model, Carbon $logDate = null, User $user = null) : Model|Null
    {
        $modelName = class_basename($model) . $event;
        $class = "\App\Actions\ActionLogs\Custom\Log{$modelName}ActionLog";

        if(class_exists($class)) {
            return app($class)->execute($model, $logDate, $user);
        }

        return null;
    }
}
