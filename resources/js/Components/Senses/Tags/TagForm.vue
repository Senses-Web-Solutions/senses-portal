<template>
    <AsideLayout v-bind="$props" :loading="state.is(AsideState.LOADING)">
        <template #title>{{ data.id ? 'Edit Tag' : 'Tag Form'}}</template>

        <SensesForm
            v-model="tag"
            v-model:fields="fields"
            v-model:error="error"
            url="/api/v2/tags"
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

            tag: {
				title: null,
				slug: null,
				colour: 'purple',
            },

            fields: [
				{
					title: "Tag Information",
					description: "Basic information about the tag",
					fields: [
						{ key: "title", type: "text"},
						{ key: "slug", type: "text"},
                        {
                            key: "tag_groups",
                            type: "select-search",
                            url: "/api/v2/tag-groups?format=select-search",
                            label: "Tag Groups",
                            relationship: "multiple",
                            relationshipKey: "tag_group_ids",
                            multiple: true,
                            preloadOptions: true
                        },
						{ key: "colour", type: "colour"},
					]
				},
            ]
        };
    },

    methods: {
        formSuccess(data) {
            eventHub.emit('tag-updated', data);
            if (this.data.id) {
                this.$asides.pop();
            } else {
                window.location.href = `/tags/${data.id}`;
            }
        }
    }
}

</script>

// Generated 09-10-2023 10:18:19
