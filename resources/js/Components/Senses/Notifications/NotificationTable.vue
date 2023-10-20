<template>
    <SensesTable :limit="100" :interactable="false" :url="url" :fields="fields" table="notifications" event="notification-updated" :rowExternalLinks="false" default-sort="created_at">
        <template #actions>
            <SecondaryButton @click="markAllRead()">Mark all as read</SecondaryButton>
        </template>
        <template #id="slotProps">
            <td :class="slotProps.class">
                {{ slotProps.row.data.title }}
            </td>
        </template>
        <template #data="slotProps">
            <td :class="slotProps.class">
                {{ slotProps.row.data.description }}
            </td>
        </template>
        <template #read_at="slotProps">
            <td :class="slotProps.class">
                <span v-if="slotProps.row.read_at">{{ formatDateTime(slotProps.row.read_at) }}</span>
                <a v-else href="#" class="text-md text-purple-500 hover:text-zinc-500 font-medium mr-2" @click="markAsRead(slotProps.row.id)">Mark as read</a>
            </td>
        </template>
        <template #url="slotProps">
            <td :class="slotProps.class">
                <a :href="slotProps?.row?.url?.href" :target="slotProps?.row?.url?.blank ? '_blank' : null" class="text-md text-purple-500 hover:text-zinc-500 font-medium mr-2">{{ slotProps?.row?.url?.title }}</a>
            </td>
        </template>
    </SensesTable>
</template>

<script>
import SensesTable from '../Tables/SensesTable.vue';
import SecondaryButton from '../../Ui/Buttons/SecondaryButton.vue';
import eventHub from '../../../Support/EventHub';
import formatDateTime from '../../../Filters/FormatDateTime';
import axios from 'axios';

export default {

    components: {
        SensesTable,
        SecondaryButton
    },

    data() {
        return {
            fields: [
                { label: "Title", key: "id", sort: false, filter: false },
                { label: "Description", key: "data", sort: false, filter: false },
                { label: "Created At", key: "created_at", format: "datetime" },
                { label: "Read At", key: "read_at", format: "template" },
                { label: "", key: "url", format: "template" },
            ],
            user: window.user
        }
    },

    methods: {
        markAsRead(notificationId) {
            axios.post(`/api/v2/users/${user().id}/notifications/${notificationId}/read`).catch(() => {
                this.$notifications.push({
                    type: 'danger',
                    title: 'Marking notification as read failed',
                    description: 'We couldn\'t mark that notification as read, please try again later.'
                });
            }).then((response) => {
                eventHub.emit('notification-updated');
            });
        },
        markAllRead() {
            axios.post(`/api/v2/users/${user().id}/notifications/read-all`).catch(() => {
                this.$notifications.push({
                    type: 'danger',
                    title: 'Marking notifications as read failed',
                    description: 'We couldn\'t mark all notifications as read, please try again later.'
                });
            }).then((response) => {
                eventHub.emit('notification-updated');
            });
        },
        formatDateTime
    },

    computed: {
        url() {
            return '/api/v2/users/' + user().id + '/notifications';
        }
    }

}
</script>
