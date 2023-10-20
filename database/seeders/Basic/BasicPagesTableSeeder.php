<?php

namespace Database\Seeders\Basic;

use App\Models\Page;
use Illuminate\Database\Seeder;

class BasicPagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Page::factory()->count(0)->create();
    }
}

//Generated 10-10-2023 14:43:35
