<?php

namespace App\Events\Files;

use App\Models\File;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class FileDeleted implements ShouldBroadcastNow
{
    public File $file;

    public function __construct(File $file)
    {
        $this->file = $file;
    }

    public function broadcastWhen()
    {
        return true;
    }

    public function broadcastOn()
    {
        return [new PrivateChannel('files.' . $this->file->id . '.main')];
    }
}
