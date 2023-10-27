<template>
    <AsideLayout v-bind="$props" :loading="state.is(AsideState.LOADING)">
        <template #title>{{ data.id ? 'Edit Server Metric' : 'Server Metric Form'}}</template>

        <SensesForm
            v-model="serverMetric"
            v-model:fields="fields"
            v-model:error="error"
            url="/api/v2/server-metrics"
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

            serverMetric: { 
				server_id: null,
				company_id: null,
				timestamp: null,
				uptime: null,
				logged_at: null,
				cpu_cores: null,
				cpu_threads: null,
				cpu_use: null,
				cpu_idle: null,
				load_1: null,
				load_5: null,
				load_15: null,
				ram_free: null,
				ram_used: null,
				disk_free: null,
				disk_used: null,
				swap_free: null,
				swap_used: null,
            },

            fields: [
				{
					title: "Server Metric Information",
					description: "Basic information about the server metric",
					fields: [
						{ key: "server_id", type: "select-search", field: "id", url: "api/v2/servers?format=select-search"},
						{ key: "company_id", type: "select-search", field: "id", url: "api/v2/companies?format=select-search"},
						{ key: "timestamp", type: "string"},
						{ key: "uptime", type: "string"},
						{ key: "logged_at", type: "string"},
						{ key: "cpu_cores", type: "number"},
						{ key: "cpu_threads", type: "number"},
						{ key: "cpu_use", type: "string"},
						{ key: "cpu_idle", type: "string"},
						{ key: "load_1", type: "string"},
						{ key: "load_5", type: "string"},
						{ key: "load_15", type: "string"},
						{ key: "ram_free", type: "number"},
						{ key: "ram_used", type: "number"},
						{ key: "disk_free", type: "number"},
						{ key: "disk_used", type: "number"},
						{ key: "swap_free", type: "number"},
						{ key: "swap_used", type: "number"},
					]
				},
            ]
        };
    },

    methods: {
        formSuccess(data) {
            eventHub.emit('server-metric-updated', data);
            if (this.data.id) {
                this.$asides.pop();
            } else {
                window.location.href = `/server-metrics/${data.id}`;
            }
        }
    }
}

</script>

// Generated 27-10-2023 10:55:27
