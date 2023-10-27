<?php

namespace Database\Seeders\Basic;

use App\Models\Company;
use Illuminate\Database\Seeder;

class BasicCompaniesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Company::factory()->count(10)->create();
    }
}

//Generated 27-10-2023 10:55:45
