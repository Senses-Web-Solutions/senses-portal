<template>
    <TimelineItem :datetime="actionLog.logged_at" :last="last">
        <template #icon>
            <DesktopComputerIcon class="h-4 w-4 text-blue-500" />
        </template>
        <template #title>
            <span class="text-black">{{ agent }}</span> requested cobrowsing
        </template>
    </TimelineItem>
</template>

<script>
import { DesktopComputerIcon } from '@heroicons/vue/outline';
import TimelineItem from '../../Timelines/TimelineItem.vue';

export default {
    components: {
        DesktopComputerIcon,
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