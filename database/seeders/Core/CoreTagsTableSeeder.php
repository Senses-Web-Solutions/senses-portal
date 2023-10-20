<?php

namespace Database\Seeders\Core;

use App\Models\Tag;
use App\Enums\LockType;
use App\Models\TagGroup;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Sequence;

class CoreTagsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $activeTag = Tag::firstOrCreate(['title' => 'Active'], [
            'colour' => 'purple-lighter',
            'text_colour' => 'purple-darker'
        ]);

        $draftTag = Tag::firstOrCreate(['title' => 'Draft'], [
            'colour' => 'gray-lighter',
            'text_colour' => 'gray-darker'
        ]);

        $pendingTag = Tag::firstOrCreate(['title' => 'Pending'], [
            'colour' => 'yellow-lighter',
            'text_colour' => 'yellow-darker'
        ]);

        $declinedTag = Tag::firstOrCreate(['title' => 'Declined'], [
            'colour' => 'red-lighter',
            'text_colour' => 'red-darker'
        ]);

        $tagGroup = TagGroup::where('slug', 'file')->first();
        $tagGroup->tags()->syncWithoutDetaching([
            $activeTag->id,
            $pendingTag->id,
            $declinedTag->id,
            $draftTag->id,
        ]);

        $tagGroup->saveQuietly();
    }
}
