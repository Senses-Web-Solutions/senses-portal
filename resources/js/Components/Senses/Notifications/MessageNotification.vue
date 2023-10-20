<template>
    <div class="relative flex flex-col space-y-2 p-5 border-b border-zinc-200">
        <div class="flex w-full items-center justify-between">
            <div class="flex items-center space-x-3">
                <p class="font-medium leading-6 text-zinc-700">
                    {{ notification.data?.title }}
                </p>
            </div>
            <div class="flex space-x-3">
                <SuccessBadge v-if="isNotificationFromToday()" size="xs">
                    New
                </SuccessBadge>
                <div>
                    <SecondaryButton size="xxs" @click="$emit('remove')">
                        <XIcon class="transition-all h-4 w-4 cursor-pointer text-zinc-500" />
                    </SecondaryButton>
                </div>
            </div>
        </div>
        <div class="flex items-end justify-between">
            <p class="text-zinc-500">
                {{ notification.data.description }}
            </p>
            <p v-if="notification.created_at" class="text-sm leading-5 text-zinc-600">
                {{ getBackendClientConfig('timeline_date_mode') == 'date_ago' ? FormatDateTimeAgo(notification.created_at) : FormatDateTime(notification.created_at) }}
            </p>
        </div>
        <!-- <div class="flex justify-between space-x-3">
                    <SeInput :id="`message-reply-${notificationIndex}`" placeholder="Type your reply" class="w-full"/>
                    <PrimaryButton> Reply </PrimaryButton>
                </div> -->
    </div>
</template>
<script>
import { parseISO, formatDistance, isWithinInterval, subDays } from 'date-fns';
import { XIcon } from '@heroicons/vue/outline';

import SecondaryButton from "../../Ui/Buttons/SecondaryButton.vue";
import SuccessBadge from '../../Ui/Badges/SuccessBadge.vue';
import FormatDateTime from '../../../Filters/FormatDateTime';
import FormatDateTimeAgo from '../../../Filters/FormatDateTimeAgo';

import { getBackendClientConfig } from '../../../Support/client';


export default {
    components: {
        XIcon,
        SecondaryButton,
        SuccessBadge
    },
    props: {
        notification: {
            type: Object,
            required: true,
        },
        index: {
            type: Number,
            required: true
        },
    },
    emits: ['remove'],
    data() {
        return {
            isReplying: false,
        };
    },
    methods: {
        FormatDateTime,
        FormatDateTimeAgo,
        getBackendClientConfig,

        toggleReplying() {
            this.isReplying = !this.isReplying;
        },
        getDate(date) {
            return formatDistance(parseISO(date), new Date(), {
                addSuffix: true,
            });
        },
        getInitials(name) {
            if (!name) {
                return '';
            }
            const parts = name.split(' ');
            let initials = '';
            for (let i = 0; i < parts.length; i += 1) {
                if (parts[i].length > 0 && parts[i] !== '') {
                    initials += parts[i][0];
                }
            }
            return initials;
        },
        isNotificationFromToday() {
            return isWithinInterval(new Date(this.notification.created_at), { start: subDays(new Date(), 1), end: new Date() })
        }
    },
};
</script>
