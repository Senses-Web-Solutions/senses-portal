<template>
    <AsideLayout v-bind="$props" :loading="state.is(AsideState.LOADING)">
        <template #title>{{ data.id ? 'Edit User' : 'User Form'}}</template>

        <SensesForm
            v-model="user"
            v-model:fields="fields"
            v-model:error="error"
            url="/api/v2/users"
            :id="data.id"
            @stateChange="state = $event"
            @success='formSuccess'
            :aside-index="asideIndex"
        />
    </AsideLayout>
</template>

<script>

import AsideLayout from '../../Layout/AsideLayout.vue';
import SensesForm from '../Common/SensesForm.vue';
import Aside from "../../../Mixins/Aside";
import eventHub from '../../../Support/EventHub';
import AsideState from "../../../States/AsideState";

export default {
    components: {
        SensesForm,
        AsideLayout,
    },

    mixins: [Aside],

    data() {
        return {
            state: new AsideState(),
            AsideState,
            error: null,

            user: {
				first_name: null,
				last_name: null,
				email: null,
				email_verified_at: null,
				password: null,
            },

            fields: [
				{
					title: "User Information",
					description: "Basic information about the user",
					fields: [
						{ key: "first_name", type: "text"},
						{ key: "last_name", type: "text"},
						{ key: "email", type: "text"},
						{ key: "password", type: "text"},
					]
				},
            ]
        };
    },

    methods: {
        formSuccess(data) {
            eventHub.emit('user-updated', data);
            if (this.data.id) {
                this.$asides.pop();
            } else {
                window.location.href = `/users/${data.id}`;
            }
        }
    }
}

</script>

// Generated 10-10-2023 10:05:13
