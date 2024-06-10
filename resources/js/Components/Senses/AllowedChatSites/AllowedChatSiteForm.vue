<template>
    <AsideLayout
        v-bind="$props"
        :loading="state.is(AsideState.LOADING)"
    >
        <template #title>{{
            data.id ? 'Edit Chat Site' : 'Chat Site Form'
        }}</template>

        <SensesForm
            :id="data.id"
            v-model="allowedChatSite"
            v-model:fields="fields"
            v-model:error="error"
            url="/api/v2/allowed-chat-sites"
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
import eventHub from '../../../Support/EventHub';
import SecondaryButton from '../../Ui/Buttons/SecondaryButton.vue';
import SeSelectSearch from '../../Ui/Inputs/SeSelectSearch.vue';
import SeSelect from '../../Ui/Inputs/SeSelect.vue';
import StrongText from '../../Ui/Text/StrongText.vue';

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

            error: null,
            allowedChatSite: {
                title: null,
                url: null,
                company_id: window.user().company_id,
            },
            fields: [
                {
                    title: 'Chat Site Information',
                    description: 'Basic information about the chat site',
                    fields: [
                        { key: 'title', type: 'text' },
                        {
                            key: 'url',
                            type: 'text',
                        },
                    ],
                },
            ],
        };
    },

    methods: {
        formSuccess(data) {
            eventHub.emit('allowed-chat-site-updated', data);

            this.$asides.pop();
        },
    },
};
// Generated 14-03-2022 09:41:07
</script>
