<?php

namespace Database\Seeders\Basic;

use Illuminate\Database\Seeder;
use Database\Seeders\Core\CoreSensesSeeder;

class BasicSensesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            CoreSensesSeeder::class,

 			BasicServersTableSeeder::class,
 			BasicServerMetricsTableSeeder::class,
 			// ----- GENERATOR -----
        ]);
    }
}
