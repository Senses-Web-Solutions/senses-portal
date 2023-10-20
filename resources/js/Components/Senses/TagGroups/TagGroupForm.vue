<template>
    <AsideLayout v-bind="$props" :loading="state.is(AsideState.LOADING)">
        <template #title>{{ data.id ? 'Edit Tag Group' : 'Tag Group Form'}}</template>

        <SensesForm
            v-model="tagGroup"
            v-model:fields="fields"
            v-model:error="error"
            url="/api/v2/tag-groups"
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

            tagGroup: {
				title: null,
            },

            fields: [
				{
					title: "Tag Group Information",
					description: "Basic information about the tag group",
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
            eventHub.emit('tag-group-updated', data);
            if (this.data.id) {
                this.$asides.pop();
            } else {
                window.location.href = `/tag-groups/${data.id}`;
            }
        }
    }
}

</script>

// Generated 09-10-2023 10:26:55
