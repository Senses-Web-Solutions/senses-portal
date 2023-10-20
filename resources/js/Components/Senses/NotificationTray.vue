<template>
    <div class="px-2 py-1 flex items-center rounded-full cursor-pointer" :class="total > 0 ? 'text-purple-600 hover:bg-purple-100 hover:text-purple-600' : 'text-zinc-400 hover:bg-zinc-100 hover:text-zinc-600'" @click="onButtonClick">
        <div class="relative">
            <div v-if="total > 0" class="absolute top-0 right-0 -mt-2 -mr-2 bg-purple-600 rounded-full px-1 border-2 border-purple-200">
                <p class="text-xs text-white font-extrabold" style="font-size:8px">{{ total }}</p>
            </div>

            <BellIcon class="h-6 w-6"></BellIcon>
        </div>

        <div class="group-hover:text-zinc-900 ml-2">
            Notifications
        </div>
    </div>
</template>

<script>
import axios from "axios";
import { formatDistance } from "date-fns";
import { computed, ref } from "vue";
import { BellIcon } from '@heroicons/vue/outline';
import { push as pushAside } from "../../Support/Asides";
import { push as pushNotification } from "../../Support/Notifications";
import useEcho from "../../Support/useEcho";
import user from "../../Support/user";
import eventHub from '../../Support/EventHub';
const echo = useEcho();

export default {
    components: { BellIcon },
    data() {
        return {
            panel: null,
            button: null,
            counts: null,
        }
    },
    mounted() {
        if (!window.localDevelopment) {
            this.loadNotifications();
        }

        eventHub.on('notification-updated', () => {
            this.loadNotifications();
        });

        echo.private(`users.${user().id}.notifications`).notification((notification) => {
            console.log(notification);
            pushNotification({
                type: 'info',
                title: notification.title || notification.message,
                description: notification.description,
                meta: notification
            })

                this.counts.notifications += 1;
        });
    },

    methods: {
        formatDate(date) {
            formatDistance(Date.parse(date), new Date(), {
                addSuffix: true,
            })
        },

        onButtonClick() {
            pushAside('NotificationAside', { noBackground: true })
        },

        loadNotifications() {
            axios.get(`/api/v2/users/${user().id}/notifications/counts`).then(({ data }) => {
                this.counts = data;
            });
        }
    },

    computed: {
        total() {
            return this.counts?.notifications;
        }
    },
};
</script>
