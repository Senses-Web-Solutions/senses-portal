<?php

namespace App\Actions;

use App\Models\Form;
use App\Models\AssignmentGroup;
use App\Models\AssignmentGroupRequirement;
use App\Models\Comment;
use App\Models\Message;
use App\Models\Task;
use Spatie\QueueableAction\QueueableAction;

class RestoreDeletedRelationships
{
    use QueueableAction;

    public function execute($modelType, $data)
    {
        switch ($modelType) {
            case 'file':
                if (isset($data['fileable_type']) && $data['fileable_type'] == 'message' && isset($data['fileable_id'])) {
                    $message = Message::withTrashed()->appFind($data['fileable_id']);
                    $this->restoreMessage($message);

                    if (isset($message)) {
                        app(RestoreDeletedRelationships::class)->execute('message', $message->toArray());
                    }
                }

                break;
            default:
                break;
        }
    }

    public function restoreMessage($message)
    {
        if (isset($message) && $message->deleted_at) {
            $message->restore();
        }
    }
}
