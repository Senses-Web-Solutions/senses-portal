<template>
    <div>
        <div class="w-full bg-zinc-50">
            <div class="px-5 py-2">
                <div class="flex justify-between">
                    <p class="my-auto capitalize font-medium text-zinc-500">
                        {{ type }} {{  }}
                    </p>
                        <SecondaryButton size="xs" class="text-xs" @click="() => $emit('removeAll')">Clear All</SecondaryButton>
                </div>
            </div>
        </div>

        <div class="overflow-y-scroll h-min max-h-96">
            <div v-for="(notification, index) in notifications" :key="notification.id">
                <GeneralNotification
                    v-if="type === 'general'"
                    :notification="notification"
                    :index="index"
                    @remove="() => $emit('remove', notification, index, type)"
                />

                <MessageNotification
                    v-if="type === 'messages'"
                    :notification="notification"
                    :index="index"
                    @remove="() => $emit('remove', notification, index, type)"
                />

                <DownloadNotification
                    v-if="type === 'downloads'"
                    :notification="notification"
                    :index="index"
                    @remove="() => $emit('remove', notification, index, type)"
                />

            </div>
        </div>
    </div>
</template>

<script>
import SecondaryButton from '../../Ui/Buttons/SecondaryButton.vue';
import GeneralNotification from './GeneralNotification.vue';
import MessageNotification from './MessageNotification.vue';
import DownloadNotification from './DownloadNotification.vue';
import EventHub from "../../../Support/EventHub";

export default {
    components: {
        SecondaryButton,
        GeneralNotification,
        MessageNotification,
        DownloadNotification,
    },
    props: {
        notifications: {
            type: Array,
            default: () => [],
        },
        transactions: {
            type: Array,
            default: () => []
        },
        type: {
            type: String,
            required: true
        }
    },
    emits: ['remove', 'removeAll'],
    methods: {
        emitNotificationDelete(notification, index) {
            console.log('Emit notification delete');
            console.log(this.notifications.length);
            EventHub.emit("notification-mark-as-read", {
                notification,
                index,
                type: this.type
            })
        },
        emitTypeNotificationDelete() {
            EventHub.emit("notification-mark-type-as-read", {
                type: this.type
            })
        }
    }
};
</script>
