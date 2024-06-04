<?php

namespace Database\Seeders\Core;

use App\Enums\LockType;
use App\Models\StatusGroup;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Sequence;

class CoreStatusGroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $titles = [
            [ 'title' => 'Page' ],
            [ 'title' => 'Chat' ],
            [ 'title' => 'Message' ]
        ];

        $nonExistingTitles = [];

        foreach ($titles as $title) {
            $statusGroup = StatusGroup::where('title', $title['title'])->select('title')->first();
            if (!isset($statusGroup)) {
                array_push($nonExistingTitles, $title);
            }
        }

        StatusGroup::factory()->count(count($nonExistingTitles))
            ->state(new Sequence(...$nonExistingTitles))
            ->create(['locked_at' => now(), 'lock_type' => LockType::CORE, 'locked_by' => 1]);
    }
}
