<?php

namespace Database\Seeders\Core;

use Illuminate\Database\Seeder;

class CoreRolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app(\App\Actions\Roles\GenerateSensesRole::class)->execute();
    }
}
