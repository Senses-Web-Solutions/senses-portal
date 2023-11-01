<?php

namespace App\Actions\Abilities;

use App\Enums\LockType;
use Illuminate\Support\Str;
use Silber\Bouncer\Bouncer;
use App\Models\AbilityGroup;
use Illuminate\Support\Collection;
use Spatie\QueueableAction\QueueableAction;

class GenerateAbilities
{
    use QueueableAction;

    protected $bouncer;

    /**
     * Create a new action instance.
     *
     * @return void
     */
    public function __construct(Bouncer $bouncer)
    {
        $this->bouncer = $bouncer;
    }

    /**
     * Execute the action.
     *
     * @return mixed
     */
    public function execute()
    {
        foreach ($this->getAbilityGroups() as $abilityGroup) {
            $abilities = collect();
            foreach ($abilityGroup['abilities'] as $ability) {
                $abilities->push($this->createAbility($ability));
            }

            $this->createAbilityGroup($abilityGroup['title'], $abilities);
        }

        foreach ($this->getIndividualAbilities() as $abilityTitle) {
            $ability = $this->createAbility($abilityTitle);
            $this->createAbilityGroup($abilityTitle, collect([$ability]));
        }

        foreach ($this->getSidebarAbilities() as $abilityTitle) {
            $ability = $this->createAbility($abilityTitle);
            $this->createAbilityGroup($abilityTitle, collect([$ability]));
        }

    }

    public function createAbility(&$ability)
    {
        return $this->bouncer->ability()->firstOrCreate([
            'name' => Str::slug($ability),
            'title' => $ability,
        ]);
    }

    public function createAbilityGroup(&$title, Collection $abilities)
    {
        $abilityGroup = AbilityGroup::firstOrNew([
            'title' => $title,
        ]);

        $abilityGroup->lock(LockType::CORE, save: false);
        $abilityGroup->save();

        $abilityGroup->abilities()->sync($abilities->pluck('id')->toArray());
        return $abilityGroup;
    }

    public function getIndividualAbilities()
    {
        return [
            'Show user setting',
            'Delete user setting',
            'Update user password',
            'Update own user password',
            'List notification',
            'List own notification',
            'List ability',
            'Impersonate user',
            'Generate report',
            'Download file',
            'Import Csv',
            'Log Out User',
            'List Hidden User',
            'Unhide user',
        ];
    }

    public function getSidebarAbilities()
    {
        return [
            'View Intel Sidebar Group'
        ];
    }

    public function getAbilityGroups()
    {
        $abilityGroups = [];
        $models = [
            'user-setting',
			'tag',
			'tag-group',
			'status',
			'status-group',
            'user',
            'file',
			'ability-group',
			'company',
			'server-metric',
			'server',
			// ----- GENERATOR -----
            //PLEASE remember to add a comma to the above, or the generator will get angry!
        ];

        foreach ($models as $model) {
            $model = str_replace('-', ' ', $model);
            array_push($abilityGroups, [
                'title' => 'Access ' . $model,
                'abilities' => [
                    'List ' . $model,
                    'Show ' . $model,
                ]
            ]);

            array_push($abilityGroups, [
                'title' => 'Manage ' . $model,
                'abilities' => [
                    'List ' . $model,
                    'Show ' . $model,
                    'Create ' . $model,
                    'Update ' . $model,
                    'Delete ' . $model,
                    'Lock ' . $model,
                ]
            ]);

            array_push($abilityGroups, [
                'title' => 'Admin ' . $model,
                'abilities' => [
                    'List ' . $model,
                    'Show ' . $model,
                    'Create ' . $model,
                    'Update ' . $model,
                    'Delete ' . $model,
                    'Lock ' . $model,
                ]
            ]);
        }

        return $abilityGroups;
    }

    public function getOwnAbilityGroup(&$abilityGroups)
    {
        $models = [
            'user-setting',
            'user',
            'page',
            'file',
        ];

        foreach ($models as $model) {
            $model = str_replace('-', ' ', $model);

            array_push($abilityGroups, [
                'title' => 'Access Own ' . $model,
                'abilities' => [
                    'List Own ' . $model,
                    'Show Own ' . $model,
                ]
            ]);

            array_push($abilityGroups, [
                'title' => 'Manage Own ' . $model,
                'abilities' => [
                    'List Own ' . $model,
                    'Show Own ' . $model,
                    'Create Own ' . $model,
                    'Update Own ' . $model,
                    'Delete Own ' . $model,
                    'Lock Own ' . $model,
                ]
            ]);

            array_push($abilityGroups, [
                'title' => 'Admin Own ' . $model,
                'abilities' => [
                    'List Own ' . $model,
                    'Show Own ' . $model,
                    'Create Own ' . $model,
                    'Update Own ' . $model,
                    'Delete Own ' . $model,
                    'Lock Own ' . $model,
                ]
            ]);
        }
    }

    public function getEarningLockDateGroup(&$abilityGroups) {
        array_push($abilityGroups, [
            'title' => 'Update date locked earning',
            'abilities' => [
                'Update date locked earning',
            ]
        ]);
    }

}
