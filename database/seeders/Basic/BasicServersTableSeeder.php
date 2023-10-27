<?php

namespace Database\Seeders\Basic;

use App\Models\Server;
use Illuminate\Database\Seeder;

class BasicServersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Server::factory()->count(10)->create();
    }
}

//Generated 27-10-2023 10:53:42
