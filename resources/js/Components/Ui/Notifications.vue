<template>
    <div class="fixed inset-0 flex items-end justify-center px-4 py-6 pointer-events-none z-[80] sm:p-6 sm:items-start sm:justify-end">
        <!--
    Notification panel, show/hide based on alert state.

    Entering: "ease-out duration-300 transition"
      From: "translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
      To: "translate-y-0 opacity-100 sm:translate-x-0"
    Leaving: "transition ease-in duration-100"
      From: "opacity-100"
      To: "opacity-0"
  -->
        <TransitionGroup
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
            enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
            leave-active-class="transition duration-100 ease-in origin-top-right"
            leave-from-class="opacity-100 sm:translate-y-0 sm:scale-100"
            leave-to-class="opacity-0 sm:translate-y-0 sm:-translate-y-6 sm:scale-50"
        >
            <div
                v-for="(notification, index) in notifs"
                :key="`notification${index}`"
                class="absolute w-full max-w-sm bg-white hover:bg-zinc-50 cursor-pointer rounded-lg shadow-lg pointer-events-auto"
                :class="notificationMargin(index)"
                        @click.stop="() => openNotifications(notification)"
            >
                <div class="overflow-hidden rounded-lg ring-1 ring-black ring-opacity-5">
                    <div class="p-4">
                        <div class="flex items-start">
                            <div class="shrink-0">
                                <CheckCircleIcon
                                    v-if="notification.type === 'success'"
                                    class="w-6 h-6 text-success-500"
                                />
                                <XCircleIcon
                                    v-if="notification.type === 'danger'"
                                    class="w-6 h-6 text-danger-500"
                                />
                                <InformationCircleIcon
                                    v-if="notification.type === 'info'"
                                    class="w-6 h-6 text-info-500"
                                />
                                <BellIcon
                                    v-if="notification.type === 'primary'"
                                    class="w-6 h-6 text-primary-500"
                                />
                                <BellIcon
                                    v-if="notification.type === 'secondary'"
                                    class="w-6 h-6 text-secondary-500"
                                />
                                <ExclamationCircleIcon
                                    v-if="notification.type === 'warning'"
                                    class="w-6 h-6 text-warning-500"
                                />
                            </div>
                            <div class="ml-3 w-0 flex-1 pt-0.5 text-black">
                                <p class="font-medium leading-5">{{ notification.title }}</p>
                                <p
                                    v-if="notification.description"
                                    class="mt-1 leading-5 text-zinc-600"
                                >{{ notification.description }}</p>
                            </div>
                            <div class="flex shrink-0 ml-4 pt-0.5">
                                <button
                                    class="inline-flex text-zinc-600 transition duration-150 ease-in-out focus:outline-none focus:text-zinc-800 rounded hover:bg-zinc-200"
                                    @click.stop="$notifications.remove(index)"
                                >
                                    <XIcon class="w-5 h-5" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </TransitionGroup>
    </div>
    <!-- my-4 my-8 my-12 my-16 my-20 my-24 -->
</template>
<script>
import {
    BellIcon,
    XIcon,
    DownloadIcon,
    CheckCircleIcon,
    InformationCircleIcon,
    XCircleIcon,
    ExclamationCircleIcon,
} from '@heroicons/vue/outline';

export default {
    components: {
        XCircleIcon,
        ExclamationCircleIcon,
        InformationCircleIcon,
        CheckCircleIcon,
        DownloadIcon,
        BellIcon,
        XIcon,
    },
    computed: {
        notifs() {
            return this.$notifications.notifications;
        },
    },
    methods: {
        notificationMargin(index) {
            if (index <= 6) {
                return [`my-${index * 4}`];
            }
            return ['my-24'];
        },
        openNotifications(notification) {
            if(notification.meta && notification.meta.url){
                if(notification.meta.url.blank){
                    window.open(notification.meta.url.href);
                } else {
                    window.location.href = notification.meta.url.href;
                }
            } else {
                this.$asides.push('NotificationAside');
            }
            this.$notifications.remove();
        }
    },
};
</script>
