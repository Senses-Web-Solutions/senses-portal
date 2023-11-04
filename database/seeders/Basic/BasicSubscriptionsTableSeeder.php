<?php

namespace Database\Seeders\Basic;

use App\Models\Subscription;
use Illuminate\Database\Seeder;

class BasicSubscriptionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Subscription::factory()->count(0)->create();
    }
}

//Generated 04-11-2023 16:09:38
