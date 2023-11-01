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
                    
				{ label: "Title", key: "title" },
				{ label: "Hostname", key: "hostname" },
				{ label: "Ip Address", key: "ip_address" },
				{ label: "Os", key: "os" },
				{ label: "Architecture", key: "architecture" },
				{ label: "Cpu Cores", key: "cpu_cores", filter: { type: "integer" } },
				{ label: "Cpu Threads", key: "cpu_threads", filter: { type: "integer" } },
				{ label: "Distro", key: "distro" },
				{ label: "Distro Version", key: "distro_version" },
				{ label: "Kernel", key: "kernel" },
				{ label: "Kernel Version", key: "kernel_version" },

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

// Generated 01-11-2023 11:27:41
