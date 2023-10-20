<template>
    <AsideLayout v-bind="$props" :loading="state.is(AsideState.LOADING)">
        <template #title>{{ data.id ? 'Edit Block Group' : 'Block Group Form'}}</template>

        <SensesForm
            v-model="blockGroup"
            v-model:fields="fields"
            v-model:error="error"
            url="/api/v2/block-groups"
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

            blockGroup: {
				display_name: null,
				name: null,
				builder_category_id: null,
				description: null,
				block_group_types: null,
            },

            fields: [
				{
					title: "Block Group Information",
					description: "Basic information about the block group",
					fields: [
						{ key: "display_name", type: "text"},
						{ key: "name", type: "text"},
						{ key: "builder_category_id", type: "select-search", field: "id", url: "api/v2/builder-categories?format=select-search"},
						{ key: "description", type: "text"},
						{ key: "block_group_types", type: "string"},
					]
				},
            ]
        };
    },

    methods: {
        formSuccess(data) {
            eventHub.emit('block-group-updated', data);
            if (this.data.id) {
                this.$asides.pop();
            } else {
                window.location.href = `/block-groups/${data.id}`;
            }
        }
    }
}

</script>

// Generated 16-10-2023 10:39:10
