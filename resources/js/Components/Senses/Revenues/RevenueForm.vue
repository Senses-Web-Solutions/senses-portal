<template>
    <AsideLayout v-bind="$props" :loading="state.is(AsideState.LOADING)">
        <template #title>{{ data.id ? 'Edit Revenue' : 'Revenue Form'}}</template>

        <SensesForm
            v-model="revenue"
            v-model:fields="fields"
            v-model:error="error"
            url="/api/v2/revenues"
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

            revenue: { 
				company_id: null,
				revenue_date: '2023-11-04 16:09:26',
				reference: null,
				description: null,
				amount: null,
				quantity: 0.00,
				vat: 0.00,
				sub_total: null,
				vat_total: null,
				total: null,
            },

            fields: [
				{
					title: "Revenue Information",
					description: "Basic information about the revenue",
					fields: [
						{ key: "company_id", type: "select-search", field: "id", url: "api/v2/companies?format=select-search"},
						{ key: "revenue_date", type: "date-time-picker"},
						{ key: "reference", type: "text"},
						{ key: "description", type: "textarea"},
						{ key: "amount", type: "number"},
						{ key: "quantity", type: "number"},
						{ key: "vat", type: "number"},
						{ key: "sub_total", type: "number"},
						{ key: "vat_total", type: "number"},
						{ key: "total", type: "number"},
					]
				},
            ]
        };
    },

    methods: {
        formSuccess(data) {
            eventHub.emit('revenue-updated', data);
            if (this.data.id) {
                this.$asides.pop();
            } else {
                window.location.href = `/revenues/${data.id}`;
            }
        }
    }
}

</script>

// Generated 04-11-2023 16:09:26
