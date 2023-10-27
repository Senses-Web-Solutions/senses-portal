<template>
    <SensesTable
        url="/api/v2/server-metrics"
        table="server_metrics"
        setting="table-server-metrics"
        event="server-metric-updated"
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
                    
				{ label: "Timestamp", key: "timestamp" },
				{ label: "Uptime", key: "uptime" },
				{ label: "Logged At", key: "logged_at", format: "datetime", filter: { type: "datetime" } },
				{ label: "Cpu Cores", key: "cpu_cores", filter: { type: "integer" } },
				{ label: "Cpu Threads", key: "cpu_threads", filter: { type: "integer" } },
				{ label: "Cpu Use", key: "cpu_use" },
				{ label: "Cpu Idle", key: "cpu_idle" },
				{ label: "Load 1", key: "load_1" },
				{ label: "Load 5", key: "load_5" },
				{ label: "Load 15", key: "load_15" },
				{ label: "Ram Free", key: "ram_free", filter: { type: "integer" } },
				{ label: "Ram Used", key: "ram_used", filter: { type: "integer" } },
				{ label: "Disk Free", key: "disk_free", filter: { type: "integer" } },
				{ label: "Disk Used", key: "disk_used", filter: { type: "integer" } },
				{ label: "Swap Free", key: "swap_free", filter: { type: "integer" } },
				{ label: "Swap Used", key: "swap_used", filter: { type: "integer" } },

            ]
        }
    },

    methods: {
        rowClick(row, type) {
            if (row.id && user().can('show-server-metric')) {
                if (type && type === 'blank') {
                    window.open("/server-metrics/" + row.id);
                } else {
                    window.location.href = "/server-metrics/" + row.id;
                }
            }
        }
    }
}

</script>

// Generated 27-10-2023 10:55:27
