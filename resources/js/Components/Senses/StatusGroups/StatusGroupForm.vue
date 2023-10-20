<template>
    <AsideLayout v-bind="$props" :loading="state.is(AsideState.LOADING)">
        <template #title>{{ data.id ? 'Edit Status Group' : 'Status Group Form'}}</template>

        <SensesForm
            v-model="statusGroup"
            v-model:fields="fields"
            v-model:error="error"
            url="/api/v2/status-groups"
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

            statusGroup: {
				title: null,
				slug: null,
            },

            fields: [
				{
					title: "Status Group Information",
					description: "Basic information about the status group",
					fields: [
						{ key: "title", type: "text"},
						{ key: "slug", type: "text"},
					]
				},
            ]
        };
    },

    methods: {
        formSuccess(data) {
            eventHub.emit('status-group-updated', data);
            if (this.data.id) {
                this.$asides.pop();
            } else {
                window.location.href = `/status-groups/${data.id}`;
            }
        }
    }
}

</script>

// Generated 09-10-2023 12:05:02
