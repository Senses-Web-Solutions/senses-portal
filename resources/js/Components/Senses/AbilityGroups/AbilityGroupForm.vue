<template>
    <AsideLayout v-bind="$props" :loading="state.is(AsideState.LOADING)">
        <template #title>{{ data.id ? 'Edit Ability Group' : 'Ability Group Form'}}</template>
        <SensesForm
            v-model="abilityGroup"
            v-model:fields="fields"
            v-model:error="error"
            url="/api/v2/ability-groups"
            :id="data.id"
            @loaded="loaded"
            @success='formSuccess'
            @stateChange="state = $event"
            :aside-index="asideIndex"
        >
         <template #abilities>
                <div class="space-y-5">
                    <SeValidation :error="error" name="newAbilities">
                        <SeSelectSearch
                            v-model="newAbilities"
                            name="newAbilities"
                            id="newAbilities"
                            :error="error"
                            :multiple="true"
                            label="Add Abilities"
                            url="/api/v2/abilities?format=select-search"
                            @update:model-value="abilityAdded"
                        />
                    </SeValidation>

                    <ReorderableList
                        :addable="false"
                        :border="abilityGroup.abilities.length > 0"
                        v-model:data="abilityGroup.abilities"
                        :orderable="false"
                        :columns="abilityColumns"
                        @update:data="setAbilityIDs"
                    >
                        <template #empty>
                            <EmptyState>This ability group has no abilities.</EmptyState>
                        </template>
                    </ReorderableList>
                </div>
            </template>
        </SensesForm>
    </AsideLayout>
</template>

<script>
import AsideLayout from '../../Layout/AsideLayout.vue';
import SensesForm from '../Common/SensesForm.vue';
import Aside from '../../../Mixins/Aside';
import eventHub from '../../../Support/EventHub';
import AsideState from "../../../States/AsideState";
import ReorderableList from '../../Ui/Lists/ReorderableList.vue';
import EmptyState from '../../Ui/EmptyState.vue';
import SeValidation from '../../Ui/Inputs/SeValidation.vue';
import SeSelectSearch from '../../Ui/Inputs/SeSelectSearch.vue';

export default {
    components: {
        SensesForm,
        AsideLayout,
        ReorderableList,
        EmptyState,
        SeValidation,
        SeSelectSearch,
    },

    mixins: [Aside],

    data() {
        return {
            state: new AsideState(),
            AsideState,
            newAbilities: [],
            abilityGroup: {
                title: null,
                abilities:[],
                ability_ids:[]
            },
            abilityColumns: [
                {
                    title: 'Ability',
                    type: 'string',
                    field: 'title',
                },
            ],
            error: null,
            fields: [
                {
                    title: 'Ability Group Information',
                    description: 'Basic information about the ability group',
                    fields: [
                        {key: 'title', type: 'text'},
                        {
                            key: 'abilities',
                            type: 'template',
                        },
                    ],
                },
            ],
        };
    },

    methods: {
        formSuccess(data) {
            eventHub.emit('ability-group-updated', data);
            this.$asides.pop();
        },

        setAbilityIDs(abilities) {
            // console.log("Setting ability Ids", abilities)
            this.abilityGroup.ability_ids = abilities.map((ability) => ability.id);
        },

        abilityAdded(value) {
            if (value.length == 0) {
                return;
            }

            this.appendAbilities(value);
            this.newAbilities = [];
        },

        appendAbilities(abilities) {
            if(!this.abilityGroup.ability_ids) {
                this.abilityGroup.ability_ids = [];
            }

            abilities = abilities.filter((ability) => {
                return this.abilityGroup.ability_ids.includes(ability.id) == false;
            });

            this.abilityGroup.abilities = this.abilityGroup.abilities.concat(abilities);
            this.abilityGroup.ability_ids = this.abilityGroup.ability_ids.concat(
                abilities.map((ability) => ability.id)
            );
        },

        loaded(abilityGroup) {
            this.abilityGroup.ability_ids = abilityGroup.abilities.map((ability) => ability.id);
        }
    },
};
</script>
