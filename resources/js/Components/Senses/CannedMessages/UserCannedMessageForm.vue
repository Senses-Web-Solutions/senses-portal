<template>
    <AsideLayout
        v-bind="$props"
        :loading="state.is(AsideState.LOADING)"
        :aside-index="asideIndex"
    >
        <template #title>{{
            data.id ? 'Edit Canned Message' : 'Canned Message Form'
        }}</template>
        <SensesForm
            :id="data.id"
            v-model="cannedMessage"
            v-model:fields="fields"
            v-model:error="error"
            url="/api/v2/canned-messages"
            :aside-index="asideIndex"
            @stateChange="state = $event"
            @success="formSuccess"
        />
    </AsideLayout>
</template>

<script>
import SecondaryButton from '../../Ui/Buttons/SecondaryButton.vue';
import AsideLayout from '../../Layout/AsideLayout.vue';
import SensesForm from '../Common/SensesForm.vue';
import Aside from '../../../Mixins/Aside';
import eventHub from '../../../Support/EventHub';
import AsideState from '../../../States/AsideState';
import user from '../../../Support/user';

export default {
    components: {
        SecondaryButton,
        SensesForm,
        AsideLayout,
    },

    mixins: [Aside],

    data() {

        return {
            state: new AsideState(),
            AsideState,
            error: null,
            cannedMessage: {
                user_id: user().id,
                title: null,
                content: null,
                system: null,
                shortcut: null,
            },
        };
    },

    computed: {
        fields: {
            get() {
                return [
                    {
                        title: 'Canned Message Information',
                        description: 'Basic information about canned Messages. ',
                        fields: [
                            { key: 'title', type: 'text' },
                            { key: 'content', type: 'textarea' },
                            { key: 'system', type: 'text' },
                            { key: 'shortcut', type: 'text' },
                        ],
                    },
                ];
            },
        },
        submitUrl() {
            if (this.data.id) {
                return `/api/v2/canned-messages/${this.data.id}`;
            }
            return '/api/v2/canned-messages';
        },
        method() {
            if (this.data.id) {
                return 'patch';
            }
            return 'post';
        },
    },

    mounted() {
        eventHub.on('canned-message-updated', () => {
            this.$asides.pop();
        });
    },

    methods: {
        formSuccess(data) {
            eventHub.emit('canned-message-updated', data);
            if (this.data.id) {
                this.$asides.pop();
            } else {
                // window.location.href = `/cannedMessages/${data.id}`;
            }
        },

        formFailure(error) {
            this.state.set(AsideState.ERROR);
            this.error = error.response.data;
        },
    },
};
// Generated 28-04-2022 15:03:20
</script>
