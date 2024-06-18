<template>
    <AsideLayout
        v-bind="$props"
        :loading="state.is(AsideState.LOADING)"
    >
        <template #title>Invite Agents to {{ data.name }}'s Chat</template>

        <SensesForm
            :id="data.id"
            v-model="formData"
            v-model:fields="fields"
            v-model:error="error"
            url="/api/v2/invite/agents"
            :aside-index="asideIndex"
            @stateChange="state = $event"
            @success="formSuccess"
        />
    </AsideLayout>
</template>

<script>
import { PlusIcon } from '@heroicons/vue/outline';
import AsideLayout from '../../Layout/AsideLayout.vue';
import ReorderableList from '../../Ui/Lists/ReorderableList.vue';
import SensesForm from '../Common/SensesForm.vue';
import Aside from '../../../Mixins/Aside';
import AsideState from '../../../States/AsideState';
import SecondaryButton from '../../Ui/Buttons/SecondaryButton.vue';
import SeSelectSearch from '../../Ui/Inputs/SeSelectSearch.vue';
import SeSelect from '../../Ui/Inputs/SeSelect.vue';
import StrongText from '../../Ui/Text/StrongText.vue';

import user from '../../../Support/user';

export default {
    components: {
        SensesForm,
        AsideLayout,
        ReorderableList,
        SecondaryButton,
        SeSelectSearch,
        SeSelect,
        StrongText,
        PlusIcon,
    },

    mixins: [Aside],

    data() {
        return {
            state: new AsideState(),
            AsideState,

            user: user(),

            error: null,
            formData: {
                chat_id: this.data.chat_id,
                agent_ids: []
            },
            fields: [
                {
                    title: 'Invite Agents',
                    description: 'Find the agent you would like to invite to this chat',
                    fields: [
                        {
                            key: "agents",
                            label: "Agents",
                            textField: 'full_name',
                            type: "select-search",
                            url: `api/v2/company/${user().company_id}/users`,
                            
                            multiple: true,
                            preloadOptions: true
                        },
                    ],
                },
            ],
        };
    },

    methods: {
        formSuccess(data) {

            this.$asides.pop();
        },
    },
};
// Generated 14-03-2022 09:41:07
</script>
