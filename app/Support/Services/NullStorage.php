<?php

namespace App\Support\Services;

use App\Models\Task;
use Illuminate\Support\Collection;
use App\Interfaces\Services\StorageService;

class NullStorage implements StorageService
{
    public function createTaskFolder(Task $task, string $cloneFolder = null) {

    }

    public function getTasksRootPath() : string {
        return '/';
    }

    public function getTaskFolderPath(Task $task) : string {
        return '/tasks/' . $task->id;
    }

    public function directories(string $directory): array
    {
        return [];
    }

    public function files(string $directory): array
    {
        return [];
    }

    public function taskDirectories(Task $task, $directory = null) : array {
        return [];
    }

    public function taskFiles(Task $task, $directory = null) : array {
        return [];
    }
}