<template>
    <div class="relative flex flex-col space-y-2 p-5 border-b border-zinc-200">
        <div class="flex justify-between items-start">
            <p class="font-medium leading-6 text-zinc-700">
                {{ notification.data?.title }}
            </p>
            <SecondaryButton size="xxs" @click="$emit('remove')">
                <XIcon class="transition-all h-4 w-4 cursor-pointer text-zinc-500" />
            </SecondaryButton>

        </div>
        <div class="flex justify-between items-end">
            <p class="text-zinc-500">
                {{ notification.data?.description }}
            </p>
            <p class="my-auto text-sm text-zinc-500">
                {{ getBackendClientConfig('timeline_date_mode') == 'date_ago' ? FormatDateTimeAgo(notification.created_at) : FormatDateTime(notification.created_at) }}
            </p>
        </div>
    </div>
</template>

<script>
import { XIcon } from '@heroicons/vue/outline';
import SecondaryButton from "../../Ui/Buttons/SecondaryButton.vue";
import FormatDateTime from '../../../Filters/FormatDateTime';
import FormatDateTimeAgo from '../../../Filters/FormatDateTimeAgo';

import { getBackendClientConfig } from '../../../Support/client';

export default {
    components: {
        XIcon,
        SecondaryButton
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
}
</script>
