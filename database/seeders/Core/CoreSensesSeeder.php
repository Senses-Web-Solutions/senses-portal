<?php

namespace Database\Seeders\Core;

use Illuminate\Database\Seeder;

class CoreSensesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(bool $importing = false)
    {
        $this->call([
            CoreAbilitiesTableSeeder::class,
            CoreRolesTableSeeder::class,
            CoreUsersTableSeeder::class,
            CoreTagGroupsTableSeeder::class,
            CoreTagsTableSeeder::class,
            CoreStatusGroupsTableSeeder::class,
            CoreStatusesTableSeeder::class,

            CoreServerSeeder::class,
        ]);
    }
}
