<?php

namespace App\Support\Services;

use Carbon\Carbon;
use App\Models\Task;
use Microsoft\Graph\Graph;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use App\Interfaces\Services\StorageService;
use Justus\FlysystemOneDrive\OneDriveAdapter;

class OneDrive implements StorageService
{
    protected $graph;
    protected $accessToken;

    
    public function getTasksRootPath() : string {
        return config('client.services.data.onedrive.tasks_folder_path', null);
    }

    public function getTaskFolderPath(Task $task) : string {
        $taskRoot = rtrim($this->getTasksRootPath(), '/');

        return $taskRoot . '/' . $task->id . ' - ' . $task->title;
    }

    public function createTaskFolder(Task $task, string $cloneFolder = null) {
        if(!$cloneFolder) {
            $cloneFolder = config('client.services.data.onedrive.tasks_folder_clone_path', null);
        }

        $path = $this->getTaskFolderPath($task);

        if(!$this->storage()->exists($path)) {
            if($cloneFolder) {
                $this->storage()->copy($cloneFolder, $path);
            }
            else {
                $this->storage()->makeDirectory($path);
            }
        }
    }

    public function taskDirectories(Task $task, $directory = null) : array {
        
        $taskDirectory = $this->getTaskFolderPath($task);

        if($directory) { 
            if(!Str::startsWith('/', $directory)) {
                $directory = '/' . $directory;
            }
            $taskDirectory = $taskDirectory . $directory;
        }
        return $this->directories($taskDirectory);
    } 

    public function taskFiles(Task $task, $directory = null) : array {
        
        $taskDirectory = $this->getTaskFolderPath($task);

        if($directory) { 
            if(!Str::startsWith('/', $directory)) {
                $directory = '/' . $directory;
            }
            $taskDirectory = $taskDirectory . $directory;
        }

        return $this->files($taskDirectory);
    } 

    public function directories(string $directory) : array {
        if(!$this->storage()->exists($directory)) {
            return [];
        }

        return $this->storage()->directories($directory);
    }

    public function files(string $directory) : array {
        if(!$this->storage()->exists($directory)) {
            return [];
        }
        
        return $this->storage()->files($directory);
    }

    //Access to onedrive token + storage adapter since we have a dynamic key and don't want octane/laravel caching an out of date key
    //we don't get a refresh token either
    public function getAccessToken() {
        if($this->accessToken) {
            return $this->accessToken;
        }

        $authority = config("client.services.data.onedrive.authority", '');

        $response = Http::asForm()->post($authority .'/oauth2/v2.0/token', [
            'client_id' => config("client.services.data.onedrive.client_id", ''),
            'client_secret' => config("client.services.data.onedrive.client_secret", ''),
            'scope' => config('client.services.data.onedrive.scopes'),
            'grant_type' => 'client_credentials'
        ]);

        $response->throw();

        $this->accessToken = $response->json()['access_token'] ?? null;

        return $this->accessToken;
    }

    public function storage() {
        $disk = Storage::build([
            'driver' => config('filesystems.disks.onedrive.driver'),
            'root' => config('filesystems.disks.onedrive.root'),
            'directory_type' => config('filesystems.disks.onedrive.directory_type'),
            'access_token' => $this->getAccessToken()
        ]);
        
        return $disk;
    }

    //use if needed instead of allFiles or allDirectories doesn't seem to work with onedrive flysystems adapter
    public function getDirectories($path) {
        $directories = collect($this->storage()->directories($path));

        foreach($directories as $directory) {
            $directories = $directories->merge($this->getDirectories($directory));
        }

        return $directories;
    }

    public function getFiles($path) {
        $files = collect($this->storage()->files($path));

        foreach($this->storage()->directories($path) as $directory) {
            $files = $files->merge($this->getFiles($directory));
        }

        return $files;
    }
}
