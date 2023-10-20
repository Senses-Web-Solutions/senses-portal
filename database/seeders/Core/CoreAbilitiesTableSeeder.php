<?php
namespace Database\Seeders\Core;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Actions\Abilities\GenerateAbilities;

class CoreAbilitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app(GenerateAbilities::class)->execute();
    }
}
