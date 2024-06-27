<template>
    <TimelineItem :datetime="actionLog.logged_at" :last="last">
        <template #icon>
            <EyeOffIcon class="h-4 w-4 text-red-500" />
        </template>
        <template #title>
            Stopped cobrowsing
        </template>
    </TimelineItem>
</template>

<script>
import { EyeOffIcon } from '@heroicons/vue/outline';
import TimelineItem from '../../Timelines/TimelineItem.vue';

export default {
    components: {
        EyeOffIcon,
        TimelineItem
    },
    props: {
        actionLog: {
            type: Object,
            required: true
        },

        last: {
            type: Boolean,
            default: false
        }
    },
    computed: {
        agent() {
            return this.actionLog.user.full_name;
        },
    },
}
</script>