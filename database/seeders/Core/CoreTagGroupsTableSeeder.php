<?php

namespace Database\Seeders\Core;

use App\Models\User;
use App\Enums\LockType;
use App\Models\Company;
use App\Models\TagGroup;
use Illuminate\Database\Seeder;
use Silber\Bouncer\Database\Role;

class CoreTagGroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(bool $importing = false)
    {
        TagGroup::factory()->create([
            'title' => 'File',
            'slug' => 'file',
            'locked_at' => now(),
            'locked_by' => 1,
            'lock_type' => LockType::CORE,
        ]);
    }
}
