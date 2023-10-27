<template>
    <SensesTable
        url="/api/v2/servers"
        table="servers"
        setting="table-servers"
        event="server-updated"
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
                    
				{ label: "Ip", key: "ip" },
				{ label: "Os", key: "os" },

            ]
        }
    },

    methods: {
        rowClick(row, type) {
            if (row.id && user().can('show-server')) {
                if (type && type === 'blank') {
                    window.open("/servers/" + row.id);
                } else {
                    window.location.href = "/servers/" + row.id;
                }
            }
        }
    }
}

</script>

// Generated 27-10-2023 10:53:42
