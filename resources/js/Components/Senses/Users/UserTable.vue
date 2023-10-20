<template>
    <SensesTable
        url="/api/v2/users"
        table="users"
        setting="table-users"
        event="user-updated"
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

				{ label: "First Name", key: "first_name" },
				{ label: "Last Name", key: "last_name" },
				{ label: "Email", key: "email" },
				{ label: "Email Verified At", key: "email_verified_at", format: "datetime", filter: { type: "datetime" } },

            ]
        }
    },

    methods: {
        rowClick(row, type) {
            if (row.id && user().can('show-user')) {
                if (type && type === 'blank') {
                    window.open("/users/" + row.id);
                } else {
                    window.location.href = "/users/" + row.id;
                }
            }
        }
    }
}

</script>

// Generated 10-10-2023 10:05:13
