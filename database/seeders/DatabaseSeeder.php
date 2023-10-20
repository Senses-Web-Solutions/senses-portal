<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\Core\CoreSensesSeeder;
use Illuminate\Support\Facades\Artisan;
use Database\Seeders\Basic\BasicSensesSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // $this->call(CoreSensesSeeder::class);
        $this->call(BasicSensesSeeder::class);

        Artisan::call('cache:clear');
    }
}
