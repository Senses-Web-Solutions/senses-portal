<?php

namespace Database\Seeders\Core;

use App\Models\Status;
use App\Enums\LockType;
use App\Models\StatusGroup;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Sequence;

class CoreStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // New, In Progress, Completed, Accepted, Declined, Pending, Closed, Done, Active, Draft, Resolved, Rejected, Duplicate
        $closedStatus = Status::firstOrCreate(['title' => 'Closed'], [ 'colour' => 'gray-light', 'text_colour' => 'gray-darker' ]);

        $doneStatus = Status::firstOrCreate(['title' => 'Done'], [ 'colour' => 'green-light', 'text_colour' => 'green-darker' ]);
        $acceptedStatus = Status::firstOrCreate(['title' => 'Accepted'], ['colour' => 'green-light', 'text_colour' => 'green-darker']);
        $completedStatus = Status::firstOrCreate(['title' => 'Completed'], [ 'colour' => 'green-light', 'text_colour' => 'green-darker' ]);

        $pendingStatus = Status::firstOrCreate(['title' => 'Pending'], [ 'colour' => 'orange-light', 'text_colour' => 'orange-darker' ]);
        $inProgressStatus = Status::firstOrCreate(['title' => 'In Progress'], [ 'colour' => 'orange-light', 'text_colour' => 'orange-darker' ]);

        $declinedStatus = Status::firstOrCreate(['title' => 'Declined'], [ 'colour' => 'red-light', 'text_colour' => 'red-darker' ]);
        $rejectedStatus = Status::firstOrCreate(['title' => 'Rejected'], [ 'colour' => 'red-light', 'text_colour' => 'red-darker' ]);

        $draftStatus = Status::firstOrCreate(['title' => 'Draft'], [ 'colour' => 'yellow-light', 'text_colour' => 'yellow-darker' ]);

        $activeStatus = Status::firstOrCreate(['title' => 'Active'], [ 'colour' => 'blue-light', 'text_colour' => 'blue-darker' ]);
        $duplicateStatus = Status::firstOrCreate(['title' => 'Duplicate'], [ 'colour' => 'blue-light', 'text_colour' => 'blue-darker' ]);

        // Chat Statuses
        $newStatus = Status::firstOrCreate(['title' => 'New'], ['colour' => 'violet-light', 'text_colour' => 'violet-darker']);
        $assignedStatus = Status::firstOrCreate(['title' => 'Assigned'], ['colour' => 'blue-light', 'text_colour' => 'blue-darker']);
        $unassignedStatus = Status::firstOrCreate(['title' => 'Unassigned'], ['colour' => 'red-light', 'text_colour' => 'red-darker']);
        $resolvedStatus = Status::firstOrCreate(['title' => 'Resolved'], ['colour' => 'green-light', 'text_colour' => 'green-darker']);
        $unresolvedStatus = Status::firstOrCreate(['title' => 'Unresolved'], ['colour' => 'red-light', 'text_colour' => 'red-darker']);
        $missedStatus = Status::firstOrCreate(['title' => 'Missed'], ['colour' => 'red-light', 'text_colour' => 'red-darker']);

        // Message Statuses
        $sentStatus = Status::firstOrCreate(['title' => 'Sent'], ['colour' => 'gray-light', 'text_colour' => 'gray-darker']);
        $receivedStatus = Status::firstOrCreate(['title' => 'Received'], ['colour' => 'green-light', 'text_colour' => 'green-darker']);
        $readStatus = Status::firstOrCreate(['title' => 'Read'], ['colour' => 'blue-light', 'text_colour' => 'blue-darker']);

        $statusGroup = StatusGroup::where('slug', 'page')->first();
        $statusGroup->statuses()->syncWithoutDetaching([
            $activeStatus->id,
            $pendingStatus->id,
            $declinedStatus->id,
            $draftStatus->id,
        ]);

        $statusGroup = StatusGroup::where('slug', 'chat')->first();
        $statusGroup->statuses()->syncWithoutDetaching([
            $newStatus->id,
            $unassignedStatus->id,
            $assignedStatus->id,
            $unresolvedStatus->id,
            $resolvedStatus->id,
            $missedStatus->id,
        ]);

        $statusGroup = StatusGroup::where('slug', 'message')->first();
        $statusGroup->statuses()->syncWithoutDetaching([
            $sentStatus->id,
            $receivedStatus->id,
            $readStatus->id,
        ]);

        $statusGroup->saveQuietly();
    }
}
