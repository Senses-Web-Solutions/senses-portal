<template>
    <AsideLayout v-bind="$props" :loading="state.is(AsideState.LOADING)">
        <template #title>{{ data.id ? 'Edit Subscription' : 'Subscription Form'}}</template>

        <SensesForm
            v-model="subscription"
            v-model:fields="fields"
            v-model:error="error"
            url="/api/v2/subscriptions"
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

            subscription: { 
				company_id: null,
				type: null,
				data: null,
            },

            fields: [
				{
					title: "Subscription Information",
					description: "Basic information about the subscription",
					fields: [
						{ key: "company_id", type: "select-search", field: "id", url: "api/v2/companies?format=select-search"},
						{ key: "type", type: "text"},
						{ key: "data", type: "string"},
					]
				},
            ]
        };
    },

    methods: {
        formSuccess(data) {
            eventHub.emit('subscription-updated', data);
            if (this.data.id) {
                this.$asides.pop();
            } else {
                window.location.href = `/subscriptions/${data.id}`;
            }
        }
    }
}

</script>

// Generated 04-11-2023 16:09:38
