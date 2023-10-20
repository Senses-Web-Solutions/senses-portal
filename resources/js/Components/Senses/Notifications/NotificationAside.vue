<template>
    <div class="absolute top-0 right-0 z-[70] w-screen max-w-2xl space-y-12 overflow-hidden p-4 sm:p-6">
        <div class="w-full space-y-4 overflow-y-auto">
            <Card flush>
                <transition enter-active-class="transition duration-300 ease-out origin-bottom" enter-from-class="opacity-0 scale-110 -translate-y-3" enter-to-class="opacity-100"
                    leave-active-class="transition duration-200 ease-in origin-bottom" leave-from-class="opacity-100" leave-to-class="opacity-0 scale-110 -translate-y-3">
                    <div>
                        <div class="flex justify-between p-5 border-b border-zinc-200">
                            <div class="flex items-center space-x-2 ">
                                <h2 class="text-xl font-medium leading-6 text-zinc-900">
                                    Notifications
                                </h2>
                                <a class="cursor-pointer" href="/notifications">
                                    <ExternalLinkIcon class="h-5 w-5 hover:text-purple" />
                                </a>
                            </div>
                            <SecondaryButton size="xs" class="text-xs" @click="markAllRead">Mark all as read</SecondaryButton>
                        </div>
                        <div v-if="loadingNotifications" class="p-5 flex justify-center">
                            <LoadingIcon class="h-6 w-6 text-primary"></LoadingIcon>
                        </div>
                        <div v-if="!loadingNotifications && notifications.length === 0 && transactions.length === 0" class="p-5 flex justify-center">
                            <EmptyState>You have 0 notifications</EmptyState>
                        </div>

                        <NotificationList
                            v-if="!loadingNotifications && general.length"
                            :notifications="general"
                            type="general"
                            @remove="(e) => markAsRead(e, 'general')"
                            @removeAll="() => markIDsAsRead('general')"
                        />

                        <NotificationList
                            v-if="!loadingNotifications && messages.length"
                            :notifications="messages"
                            type="messages"
                            @remove="(e) => markAsRead(e, 'messages')"
                            @removeAll="() => markIDsAsRead('messages')"
                        />

                        <NotificationList
                            v-if="!loadingNotifications && downloads.length"
                            :notifications="downloads"
                            type="downloads"
                            @remove="(e) => markAsRead(e, 'downloads')"
                            @removeAll="() => markIDsAsRead('downloads')"
                        />

                        <div v-if="!loadingNotifications && transactions.length">
                            <div v-for="(transaction, transactionIndex) in transactions" :key="transaction.id">
                                <TransactionNotification :transaction="transaction" @remove="removeTransaction(transaction, transactionIndex)" />
                            </div>
                        </div>

                        <NotificationList
                            v-if="!loadingNotifications && slas.length"
                            :notifications="slas"
                            type="slas"
                            @remove="(e) => markAsRead(e, 'slas')"
                            @removeAll="() => markIDsAsRead('slas')"
                        />
                    </div>
                </transition>
            </Card>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import {
    ExternalLinkIcon
} from '@heroicons/vue/outline';
import NotificationList from './NotificationList.vue';
import LoadingIcon from '../../Ui/LoadingIcon.vue';
import Card from '../../Ui/Cards/Card.vue';
import EmptyState from '../../Ui/EmptyState.vue';
import SecondaryButton from '../../Ui/Buttons/SecondaryButton.vue';
import user from '../../../Support/user';
import eventHub from '../../../Support/EventHub';
import TransactionNotification from './TransactionNotification.vue';

export default {
    components: {
        LoadingIcon,
        ExternalLinkIcon,
        NotificationList,
        EmptyState,
        Card,
        SecondaryButton,
        TransactionNotification
    },

    data() {
        return {
            transactions: [],
            messages: [],
            downloads: [],
            slas: [],
            general: [],
            loadingNotifications: false,
        };
    },

    computed: {
        notifications() {
            return [...this.general, ...this.downloads, ...this.slas, ...this.messages];
        }
    },

    mounted() {
        this.loadingNotifications = true;

        this.loadTransactions();
        this.loadNotifications();
    },

    methods: {
        loadNotifications() {
            axios
                .get(`/api/v2/users/${user().id}/notifications?filter[unread]=true`)
                .then(({
                    data
                }) => {
                    this.general = data.filter(
                        (notification) =>
                            notification.type !== 'App\\Notifications\\MessageReceived' &&
                            notification.type !== 'App\\Notifications\\ExportDownloadReady' &&
                            notification.type !== 'App\\Notifications\\SlaOneHourRemaining' &&
                            notification.type !== 'App\\Notifications\\PrintReady' &&
                            notification.type !== 'App\\Notifications\\ReportDownloadReady'
                    );
                    this.messages = data.filter(
                        (notification) => notification.type === 'App\\Notifications\\MessageReceived'
                    );
                    this.downloads = data.filter(
                        (notification) =>
                            notification.type === 'App\\Notifications\\ExportDownloadReady' ||
                            notification.type === 'App\\Notifications\\PrintReady' ||
                            notification.type === 'App\\Notifications\\ReportDownloadReady'
                    );
                    this.slas = data.filter(
                        (notification) => notification.type === 'App\\Notifications\\SlaOneHourRemaining'
                    );
                    this.loadingNotifications = false;
                });
        },
        loadTransactions() {
            axios.get(`/api/v2/users/${user().id}/transactions?filter[status]=pending,in-progress`).then(({
                data
            }) => {
                this.transactions = data.data;
                this.loadingNotifications = false;
            })
        },
        markAsRead(notification, type) {
            const notificationIndex = this[type].findIndex((element) => element.id === notification.id);

            axios
                .post(
                    `/api/v2/users/${user().id}/notifications/${notification.id
                    }/read`
                )
                .then(() => {
                    eventHub.emit('notification-updated');
                })
                .catch(() => {
                    this[type].splice(
                        notificationIndex,
                        0,
                        notification
                    );
                    this.$notifications.push({
                        type: 'danger',
                        title: 'Marking notification as read failed',
                        description: "We couldn't mark that notification as read, please try again later.",
                    });
                });

            this[type].splice(notificationIndex, 1);
        },
        markIDsAsRead(type) {
            const notificationBackup = this[type];
            const notificationIDs = this[type].map(notification => notification.id);

            const data = {
                ids: notificationIDs,
            };

            axios.post(`/api/v2/users/${user().id}/notifications/read`, data)
                .then(() => {
                    eventHub.emit('notification-updated');
                    this.loadNotifications();

                    if (type === 'downloads') {
                        this.transactions = [];
                    }
                    this[type] = [];

                })
                .catch(() => {
                    this[type] = notificationBackup;
                    this.$notifications.push({
                        type: 'danger',
                        title: `Marking ${type} notifications as read failed`,
                        description: `We couldn't mark all ${type} notifications as read, please try again later.`,
                    });
                });
        },
        markAllRead() {
            this.loadingNotifications = true;
            const generalBackup = this.general;
            const slasBackup = this.slas;
            const messagesBackup = this.messages;
            const downloadsBackup = this.downloads;

            const notificationIDs = this.notifications.map(notification => notification.id);

            const data = {
                ids: notificationIDs,
            };

            axios.post(`/api/v2/users/${user().id}/notifications/read`, data)
                .then(() => {
                    eventHub.emit('notification-updated');
                    this.loadNotifications();

                    this.general = [];
                    this.slas = [];
                    this.downloads = [];
                    this.messages = [];
                    this.transactions = [];

                })
                .catch(() => {
                    this.general = generalBackup;
                    this.slas = slasBackup;
                    this.downloads = messagesBackup;
                    this.messages = downloadsBackup;
                    this.$notifications.push({
                        type: 'danger',
                        title: `Marking all notifications as read failed`,
                        description: `We couldn't mark all notifications as read, please try again later.`,
                    });
                });
        },
        removeTransaction(transaction, index) {
            this.transactions.splice(index, 1);
        },
    },
};
</script>
