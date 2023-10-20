<?php

namespace Database\Seeders\Basic;

use App\Models\BlockGroup;
use Illuminate\Database\Seeder;

class BasicBlockGroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        BlockGroup::factory()->count(10)->create();
    }
}

//Generated 16-10-2023 10:39:10
