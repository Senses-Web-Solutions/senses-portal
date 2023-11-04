<?php

namespace Database\Seeders\Basic;

use App\Models\CommunicationLog;
use Illuminate\Database\Seeder;

class BasicCommunicationLogsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CommunicationLog::factory()->count(0)->create();
    }
}

//Generated 04-11-2023 16:09:50
