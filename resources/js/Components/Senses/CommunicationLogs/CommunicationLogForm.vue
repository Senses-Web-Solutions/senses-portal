<template>
    <AsideLayout v-bind="$props" :loading="state.is(AsideState.LOADING)">
        <template #title>{{ data.id ? 'Edit Communication Log' : 'Communication Log Form'}}</template>

        <SensesForm
            v-model="communicationLog"
            v-model:fields="fields"
            v-model:error="error"
            url="/api/v2/communication-logs"
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

            communicationLog: { 
				company_id: null,
				type: null,
				data: null,
            },

            fields: [
				{
					title: "Communication Log Information",
					description: "Basic information about the communication log",
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
            eventHub.emit('communication-log-updated', data);
            if (this.data.id) {
                this.$asides.pop();
            } else {
                window.location.href = `/communication-logs/${data.id}`;
            }
        }
    }
}

</script>

// Generated 04-11-2023 16:09:50
