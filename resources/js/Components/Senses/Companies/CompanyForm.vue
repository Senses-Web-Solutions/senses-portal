<template>
    <AsideLayout v-bind="$props" :loading="state.is(AsideState.LOADING)">
        <template #title>{{ data.id ? 'Edit Company' : 'Company Form'}}</template>

        <SensesForm
            v-model="company"
            v-model:fields="fields"
            v-model:error="error"
            url="/api/v2/companies"
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

            company: { 
				title: null,
				slug: null,
            },

            fields: [
				{
					title: "Company Information",
					description: "Basic information about the company",
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
            eventHub.emit('company-updated', data);
            if (this.data.id) {
                this.$asides.pop();
            } else {
                window.location.href = `/companies/${data.id}`;
            }
        }
    }
}

</script>

// Generated 27-10-2023 10:55:45
