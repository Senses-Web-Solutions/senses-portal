<template>
    <div class="flex justify-end items-center select-none">
            <p class="text-sm mr-1">{{ FormatTime(message.sent_at, 'HH:mm') }}</p>
            <div v-if="message.from_agent" class="leading-none last:mr-0 text-gray-500 z-10" :class="{ '!text-blue-500': message.sent_at }">
                <CheckIcon class="h-4 w-4" />
            </div>
            <div v-if="message.from_agent" class="leading-none text-gray-500 -ml-3 z-0" :class="{ '!text-blue-500': message.read_at }">
                <CheckIcon class="h-4 w-4" />
            </div>
        </div>
</template>
<script>
import { CheckIcon } from '@heroicons/vue/outline';

import FormatTime from '../../../Filters/FormatTime';

export default {
    components: {
        CheckIcon,
    },
    props: {
        message: {
            type: Object,
            required: true,
        },
    },
    methods: {
        FormatTime,
    },
}
</script>