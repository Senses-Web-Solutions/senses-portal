<?php

namespace App\Support\Services;

use App\Interfaces\Services\TaskService;
use App\Models\Asset;
use App\Models\Task;
use Illuminate\Support\Carbon;

class NullTask implements TaskService
{
    public function submitTask(Task|int $task)
    {
        
    }
}
