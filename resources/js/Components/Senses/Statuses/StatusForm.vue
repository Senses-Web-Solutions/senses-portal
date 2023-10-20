<template>
    <AsideLayout v-bind="$props" :loading="state.is(AsideState.LOADING)">
        <template #title>{{ data.id ? 'Edit Status' : 'Status Form'}}</template>

        <SensesForm
            v-model="status"
            v-model:fields="fields"
            v-model:error="error"
            url="/api/v2/statuses"
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

            status: {
				title: null,
				slug: null,
				colour: 'purple',
            },

            fields: [
				{
					title: "Status Information",
					description: "Basic information about the status",
					fields: [
						{ key: "title", type: "text"},
						{ key: "slug", type: "text"},
						{ key: "colour", type: "colour"},
					]
				},
            ]
        };
    },

    methods: {
        formSuccess(data) {
            eventHub.emit('status-updated', data);
            if (this.data.id) {
                this.$asides.pop();
            } else {
                window.location.href = `/statuses/${data.id}`;
            }
        }
    }
}

</script>

// Generated 09-10-2023 12:35:29
