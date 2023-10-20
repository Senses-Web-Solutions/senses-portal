<?php

namespace Database\Seeders\Basic;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Sequence;

class BasicTagsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $titles = [
            "Tag"
        ];

        Tag::factory()
        ->count(count($titles))
        ->sequence(function (Sequence $sequence) use(&$titles) {
            return [
                'title' => $titles[$sequence->index],
            ];
        })
        ->create();
    }
}

//Generated 09-10-2023 10:18:19
