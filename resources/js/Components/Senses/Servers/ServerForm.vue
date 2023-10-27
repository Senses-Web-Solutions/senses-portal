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

            server: { 
				company_id: null,
				title: null,
				slug: null,
				ip: null,
				os: null,
				priority: null,
            },

            fields: [
				{
					title: "Server Information",
					description: "Basic information about the server",
					fields: [
						{ key: "company_id", type: "select-search", field: "id", url: "api/v2/companies?format=select-search"},
						{ key: "title", type: "text"},
						{ key: "slug", type: "text"},
						{ key: "ip", type: "text"},
						{ key: "os", type: "text"},
						{ key: "priority", type: "number"},
					]
				},
            ]
        };
    },

    methods: {
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

// Generated 27-10-2023 10:53:42
