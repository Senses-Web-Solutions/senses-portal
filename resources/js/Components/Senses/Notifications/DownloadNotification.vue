<template>
    <div class="relative flex flex-col p-5 border-b border-zinc-200">
        <div class="flex items-start justify-between">
            <div>
                <p class="font-medium text-zinc-700">
                    {{ notification.data?.title }}
                </p>
                <p v-if="notification.data?.secondary_title" class="text-sm text-zinc-700 pt-1">
                    {{ notification.data?.secondary_title }}
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <SuccessBadge size="xs">Complete</SuccessBadge>
                <div>
                    <SecondaryButton size="xxs" rounded="left" class="translate-x-[1px]">
                        <a v-if="notification?.url" :href="notification?.url?.href" :target="notification?.url?.blank ? '_blank' : null" class="text-sm text-zinc-600 hover:text-purple-700">
                            <DownloadIcon class="h-4 w-4 text-zinc-500" />
                        </a>
                    </SecondaryButton>
                    <SecondaryButton size="xxs" rounded="right" @click="$emit('remove')">
                        <XIcon class="transition-all h-4 w-4 cursor-pointer text-zinc-500" />
                    </SecondaryButton>
                </div>
            </div>
        </div>
        <div class="flex items-center justify-between">
            <span class="h-2 w-[85%] rounded-md bg-zinc-100">
                <span class="block h-2 w-full rounded-md bg-green-600"></span>
            </span>
            <p v-if="notification.created_at" class="text-sm leading-5 text-zinc-600 text-right">
                {{ getBackendClientConfig('timeline_date_mode') == 'date_ago' ? FormatDateTimeAgo(notification.created_at) : FormatDateTime(notification.created_at) }}
            </p>
        </div>
    </div>
</template>

<script>
import { DownloadIcon, XIcon } from '@heroicons/vue/outline';
import SecondaryButton from "../../Ui/Buttons/SecondaryButton.vue";
import SuccessBadge from "../../Ui/Badges/SuccessBadge.vue";
import FormatDateTime from '../../../Filters/FormatDateTime';
import FormatDateTimeAgo from '../../../Filters/FormatDateTimeAgo';

import { getBackendClientConfig } from '../../../Support/client';

export default {
    components: {
        DownloadIcon,
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
        }
    },
    emits: ['remove'],
    methods: {
        FormatDateTime,
        FormatDateTimeAgo,
        getBackendClientConfig,
    },
};
</script>
