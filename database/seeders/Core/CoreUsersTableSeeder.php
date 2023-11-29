<?php

namespace Database\Seeders\Core;

use App\Models\User;
use App\Enums\LockType;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Silber\Bouncer\Database\Role;
use App\Actions\Roles\AssignRoleToUser;

class CoreUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(bool $importing = false)
    {
        $users = [
            [
                'first_name' => 'Senses',
                'last_name' => 'System',
                'full_name' => 'Senses System',
                'email' => 'senses@senses.co.uk',
            ],
        ];

        if (!$importing) {
            $users = array_merge($users, [
                [
                    'first_name' => 'Matt',
                    'last_name' => 'Hiscock',
                    'email' => 'matt@senses.co.uk',
                ],
                [
                    'first_name' => 'Ian',
                    'last_name' => 'Lawton',
                    'email' => 'ian@senses.co.uk',
                ],
                [
                    'first_name' => 'Rob',
                    'last_name' => 'Lees',
                    'email' => 'rob@senses.co.uk',
                ],
                [
                    'first_name' => 'Ross',
                    'last_name' => 'Dexter',
                    'email' => 'ross@senses.co.uk',
                ],
                [
                    'first_name' => 'Bethany',
                    'last_name' => 'Armitage',
                    'email' => 'bethany@senses.co.uk',
                ],
                [
                    'first_name' => 'Brad',
                    'last_name' => 'Witt',
                    'email' => 'brad.witt@footprintmapping.co.uk',
                ],
                [
                    'first_name' => 'Jack',
                    'last_name' => 'Ashman',
                    'email' => 'jack@senses.co.uk',
                ],
                [
                    'first_name' => 'Josh',
                    'last_name' => 'Dovey',
                    'email' => 'josh@senses.co.uk',
                ],
                [
                    'first_name' => 'Tilly',
                    'last_name' => 'Cooper',
                    'email' => 'tilly@senses.co.uk',
                ]
            ]);
        }

        $role = Role::where('name', 'senses')->firstOrFail();

        foreach ($users as $userData) {
            $user = User::factory()->create(array_merge($userData, [
                'password' => '$2y$10$xfqkRSdhbc8BK9MsN3OzJuN6qXDnNIgMvfm9YTA.QedNE2Agwhbwq',
                'company_id' => 1,
                'locked_at' => now(),
                'locked_by' => 1,
                'lock_type' => LockType::CORE,
            ]));

            app(AssignRoleToUser::class)->execute($role, $user);
        }
    }
}
