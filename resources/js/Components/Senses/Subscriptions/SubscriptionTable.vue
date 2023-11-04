<template>
    <SensesTable
        url="/api/v2/subscriptions"
        table="subscriptions"
        setting="table-subscriptions"
        event="subscription-updated"
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
                    
				{ label: "Type", key: "type" },

            ]
        }
    },

    methods: {
        rowClick(row, type) {
            if (row.id && user().can('show-subscription')) {
                if (type && type === 'blank') {
                    window.open("/subscriptions/" + row.id);
                } else {
                    window.location.href = "/subscriptions/" + row.id;
                }
            }
        }
    }
}

</script>

// Generated 04-11-2023 16:09:38
