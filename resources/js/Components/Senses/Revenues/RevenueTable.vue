<template>
    <SensesTable
        url="/api/v2/revenues"
        table="revenues"
        setting="table-revenues"
        event="revenue-updated"
        :fields="fields"
        :actions="actions"
        @row-click="rowClick"
    />
</template>

<script>

import SensesTable from '../Tables/SensesTable.vue';

export default {
    components: {
        SensesTable,
    },

    data() {
        return {
            user: window.user,
            actions: [],

            fields: [
                { label: "ID", key: "id" },
                    
				{ label: "Revenue Date", key: "revenue_date" },
				{ label: "Reference", key: "reference" },
				{ label: "Description", key: "description" },
				{ label: "Amount", key: "amount", format: "money" },
				{ label: "Quantity", key: "quantity" },
				{ label: "Vat", key: "vat" },
				{ label: "Sub Total", key: "sub_total", format: "money" },
				{ label: "Vat Total", key: "vat_total", format: "money" },
				{ label: "Total", key: "total", format: "money" },

            ]
        }
    },

    methods: {
        rowClick(row, type) {
            if (row.id && user().can('show-revenue')) {
                if (type && type === 'blank') {
                    window.open("/revenues/" + row.id);
                } else {
                    window.location.href = "/revenues/" + row.id;
                }
            }
        }
    }
}

</script>

// Generated 04-11-2023 16:09:26
