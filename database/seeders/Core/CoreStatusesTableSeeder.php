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

        $newStatus = Status::firstOrCreate(['title' => 'New'], [ 'colour' => 'violet-light', 'text_colour' => 'violet-darker' ]);
        $closedStatus = Status::firstOrCreate(['title' => 'Closed'], [ 'colour' => 'gray-light', 'text_colour' => 'gray-darker' ]);

        $doneStatus = Status::firstOrCreate(['title' => 'Done'], [ 'colour' => 'green-light', 'text_colour' => 'green-darker' ]);
        $acceptedStatus = Status::firstOrCreate(['title' => 'Accepted'], [ 'colour' => 'green-light', 'text_colour' => 'green-darker' ]);
        $resolvedStatus = Status::firstOrCreate(['title' => 'Resolved'], [ 'colour' => 'green-light', 'text_colour' => 'green-darker' ]);
        $completedStatus = Status::firstOrCreate(['title' => 'Completed'], [ 'colour' => 'green-light', 'text_colour' => 'green-darker' ]);

        $pendingStatus = Status::firstOrCreate(['title' => 'Pending'], [ 'colour' => 'orange-light', 'text_colour' => 'orange-darker' ]);
        $inProgressStatus = Status::firstOrCreate(['title' => 'In Progress'], [ 'colour' => 'orange-light', 'text_colour' => 'orange-darker' ]);

        $declinedStatus = Status::firstOrCreate(['title' => 'Declined'], [ 'colour' => 'red-light', 'text_colour' => 'red-darker' ]);
        $rejectedStatus = Status::firstOrCreate(['title' => 'Rejected'], [ 'colour' => 'red-light', 'text_colour' => 'red-darker' ]);

        $draftStatus = Status::firstOrCreate(['title' => 'Draft'], [ 'colour' => 'yellow-light', 'text_colour' => 'yellow-darker' ]);

        $activeStatus = Status::firstOrCreate(['title' => 'Active'], [ 'colour' => 'blue-light', 'text_colour' => 'blue-darker' ]);
        $duplicateStatus = Status::firstOrCreate(['title' => 'Duplicate'], [ 'colour' => 'blue-light', 'text_colour' => 'blue-darker' ]);

        $statusGroup = StatusGroup::where('slug', 'page')->first();
        $statusGroup->statuses()->syncWithoutDetaching([
            $activeStatus->id,
            $pendingStatus->id,
            $declinedStatus->id,
            $draftStatus->id,
        ]);

        $statusGroup->saveQuietly();
    }
}
