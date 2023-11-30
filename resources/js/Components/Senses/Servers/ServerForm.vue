<template>
    <AsideLayout v-bind="$props" :loading="state.is(AsideState.LOADING)">
        <template #title>{{ data.id ? 'Edit Server' : 'Server Form'}}</template>

        <SensesForm
            v-model="server"
            v-model:fields="fields"
            v-model:error="error"
            url="/api/v2/servers"
            :id="data.id"
            @stateChange="state = $event"
            @success='formSuccess'
            :aside-index="asideIndex"
            :onSubmit="save"
        />
    </AsideLayout>
</template>

<script>

import AsideLayout from '../../Layout/AsideLayout.vue';
import SensesForm from '../Common/SensesForm.vue';
import Aside from "../../../Mixins/Aside";
import eventHub from '../../../Support/EventHub';
import AsideState from "../../../States/AsideState";

import axios from 'axios';

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

            server: {
				company_id: null,
				title: null,
				hostname: null,
				ip_address: null,
				os: null,
				architecture: null,
				cpu_cores: null,
				cpu_threads: null,
				distro: null,
				distro_version: null,
				kernel: null,
				kernel_version: null,
            },

            fields: [
				{
					title: "Server Information",
					description: "Basic information about the server",
					fields: [
						// { key: "company_id", type: "select-search", field: "id", url: "api/v2/companies?format=select-search"},
						{ key: "title", type: "text"},
						// { key: "hostname", type: "text"},
						// { key: "ip_address", type: "text"},
						{ label: 'Operating System', key: "os", type: "select", field: 'title', options: [{id: 1, title: 'Linux'}]},
						// { key: "architecture", type: "text"},
						// { key: "cpu_cores", type: "number"},
						// { key: "cpu_threads", type: "number"},
						// { key: "distro", type: "text"},
						// { key: "distro_version", type: "text"},
						// { key: "kernel", type: "text"},
						// { key: "kernel_version", type: "text"},
					]
				},
            ]
        };
    },

    methods: {
        save() {
            axios.post('/api/v2/servers', this.server).then((response) => {
                // console.log(response);
                this.$asides.pop();

                const confirmed = this.$modals.push('ServerSetup', response.data);
            });
        },

        formSuccess(data) {
            eventHub.emit('server-updated', data);
            if (this.data.id) {
                this.$asides.pop();
            } else {
                window.location.href = `/servers/${data.id}`;
            }
        }
    }
}

</script>

// Generated 01-11-2023 11:27:41
