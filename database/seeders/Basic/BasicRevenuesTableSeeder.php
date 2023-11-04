<?php

namespace Database\Seeders\Basic;

use App\Models\Revenue;
use Illuminate\Database\Seeder;

class BasicRevenuesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Revenue::factory()->count(0)->create();
    }
}

//Generated 04-11-2023 16:09:26
