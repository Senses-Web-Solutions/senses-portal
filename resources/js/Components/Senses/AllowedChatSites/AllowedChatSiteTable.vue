<template>
    <SensesTable
        :url="`/api/v2/company/${user().company_id}/allowed-chat-sites`"
        table="allowed_chat_sites"
        setting="table-allowed-chat-sites"
        event="allowed-chat-sites-updated"
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
				{ label: "Url", key: "url" },
            ]
        }
    },

    methods: {
        rowClick(row, type) {
            if (row.id && user().can('show-allowed-chat-site')) {
                this.$asides.push('AllowedChatSiteView', { id: row.id });
            }
        }
    }
}

</script>

// Generated 09-10-2023 10:26:55
