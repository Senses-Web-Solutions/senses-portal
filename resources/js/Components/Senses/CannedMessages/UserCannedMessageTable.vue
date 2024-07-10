<template>
    <SensesTable
        title="Canned Messages"
        :url="`/api/v2/users/${user().id}/canned-messages`"
        table="canned_messages"
        setting="table-canned-messages"
        event="canned-message-updated"
        :fields="fields"
        :actions="actions"
        :plus-button="plusButton"
        @row-click="rowClick"
    />
</template>

<script>
import { PlusIcon } from '@heroicons/vue/outline';
import SensesTable from '../Tables/SensesTable.vue';

export default {
    components: {
        SensesTable,
        PlusIcon
    },

    data() {
        return {
            user: window.user,
            actions: [],

            fields: [
                { label: "ID", key: "id" },
                { label: 'Title', key: 'title' },
				{ label: "Content", key: "content" },
				{ label: "System", key: "system" },
                { label: "Shortcut", key: "shortcut" },
            ],

            plusButton: {
                    data: {},
                    disabled: !user().can('create-canned-message'),
                    form: 'UserCannedMessageForm',
            },
        }
    },

    methods: {
        rowClick(row, type) {
            // if (row.id && user().can('show-chat-review')) {
            //     if (type && type === 'blank') {
            //         window.open("/chat-reviews/" + row.id);
            //     } else {
            //         window.location.href = "/chat-reviews/" + row.id;
            //     }
            // }
        }
    }
}

</script>
