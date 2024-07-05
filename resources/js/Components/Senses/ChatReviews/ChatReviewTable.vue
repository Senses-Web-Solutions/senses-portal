<template>
    <SensesTable
        title="Chat Reviews"
        url="/api/v2/chat-reviews"
        table="chat_reviews"
        setting="table-chat-reviews"
        event="chat-review-updated"
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
                { label: 'Chat', key: 'chat_id' },
				{ label: "Knowledge", key: "knowledge" },
				{ label: "Friendliness", key: "friendliness" },
                { label: "Responsiveness", key: "responsiveness" },
                { label: "Overall", key: "overall" },
                { label: "Resolved", key: "resolved" },
                { label: "Comment", key: "comment" },
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
