<template>
    <SensesTable
        title="Chat Users"
        :url="`/api/v2/company/${user().company_id}/chat-users`"
        table="chat_users"
        setting="table-chat-users"
        event="chat-user-updated"
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
                { label: 'Full Name', key: 'full_name' },
				{ label: "Email", key: "email" },
				{ label: "System", key: "system" },
            ]
        }
    },

    methods: {
        rowClick(row, type) {
            if (row.id && user().can('show-chat-user')) {
                if (type && type === 'blank') {
                    window.open("/chat-users/" + row.id);
                } else {
                    window.location.href = "/chat-users/" + row.id;
                }
            }
        }
    }
}

</script>

// Generated 27-10-2023 10:55:45
