<template>
    <AsideLayout v-bind="$props" :loading="state.is(AsideState.LOADING)">
        <template #title>{{ data.id ? 'Edit Page' : 'Page Form'}}</template>

        <SensesForm
            v-model="page"
            v-model:fields="fields"
            v-model:error="error"
            url="/api/v2/pages"
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

            page: {
				title: null,
				slug: null,
				excerpt: null,
				type: null,
				status_id: null,
				meta_title: null,
				meta_description: null,
				featured: false,
				show_last_updated: false,
            },

            fields: [
				{
					title: "Page Information",
					description: "Basic information about the page",
					fields: [
						{ key: "title", type: "text"},
						{ key: "slug", type: "text"},
						{ key: "excerpt", type: "textarea"},
						{ key: "type", type: "text"},
						{ key: "status_id", type: "select-search", field: "id", url: "api/v2/statuses?format=select-search&filter[status-group-slug]=page"},
						{ key: "meta_title", type: "text"},
						{ key: "meta_description", type: "textarea"},
						{ key: "featured", type: "toggle"},
						{ key: "show_last_updated", type: "toggle"},
					]
				},
            ]
        };
    },

    methods: {
        formSuccess(data) {
            eventHub.emit('page-updated', data);
            if (this.data.id) {
                this.$asides.pop();
            } else {
                window.location.href = `/pages/${data.id}`;
            }
        }
    }
}

</script>

// Generated 10-10-2023 14:43:35
