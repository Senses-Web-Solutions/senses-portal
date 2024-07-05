<template>
    <SensesTable
        title="Chats"
        :url="`/api/v2/chat-users/${chatUser.id}/chats`"
        table="chats"
        setting="table-chats"
        event="chat-updated"
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

    props: {
        chatUser: {
            type: Object,
            required: true,
        },
    },

    data() {
        return {
            user: window.user,
            actions: [],

            fields: [
                { label: "ID", key: "id" },
                { label: "System", key: "system" },
                { label: "Started At", key: 'created_at' },
                { label: "Completed At", key: "completed_at" },
            ]
        }
    },

    methods: {
        rowClick(row, type) {
            if (row.id && user().can('show-chat-review')) {
                if (type && type === 'blank') {
                    window.open("/chat-reviews/" + row.id);
                } else {
                    window.location.href = "/chat-reviews/" + row.id;
                }
            }
        }
    }
}

</script>

// Generated 27-10-2023 10:55:45
