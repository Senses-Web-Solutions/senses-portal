<template>
    <AsideLayout
        v-bind="$props"
        :loading="state.is(AsideState.LOADING)"
        :aside-index="asideIndex"
    >
        <template #title>{{
            data.id ? 'Edit Chat User' : 'Chat User Form'
        }}</template>
        <SensesForm
            :id="data.id"
            v-model="chatUser"
            v-model:fields="fields"
            v-model:error="error"
            url="/api/v2/chat-users"
            :aside-index="asideIndex"
            @submit="submit"
            @stateChange="state = $event"
            @success="formSuccess"
        />
    </AsideLayout>
</template>

<script>
import axios from 'axios';
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
            chatUser: {
                first_name: null,
                last_name: null,
                email: null,
                system: null,
                company_id: user().company_id,
            },
        };
    },

    computed: {
        fields: {
            get() {
                return [
                    {
                        title: 'Chat User Information',
                        description: 'Basic information about the chat user.',
                        fields: [
                            { key: 'first_name', type: 'text' },
                            { key: 'last_name', type: 'text' },
                            { key: 'email', type: 'email' },
                            { key: 'system', type: 'text' },
                        ],
                    },
                ];
            },
        },
        submitUrl() {
            if (this.data.id) {
                return `/api/v2/chat-users/${this.data.id}`;
            }
            return '/api/v2/chat-users';
        },
        method() {
            if (this.data.id) {
                return 'patch';
            }
            return 'post';
        },
    },

    methods: {

        submit() {
            this.state.set(AsideState.LOADING);
            this.error = null;

            this.chatUser.full_name = `${this.chatUser.first_name} ${this.chatUser.last_name}`;

            axios[this.method](this.submitUrl, this.chatUser)
                .then(({ data }) => this.formSuccess(data))
                .catch((error) => this.formFailure(error));
        },

        formSuccess(data) {
            eventHub.emit('chat-user-updated', data);
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
