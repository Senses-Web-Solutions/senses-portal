<template>
    <TimelineItem :datetime="actionLog.logged_at" :last="last">
        <template #icon>
            <EyeIcon class="h-4 w-4 text-green-500" />
        </template>
        <template #title>
            Accepted cobrowsing
        </template>
    </TimelineItem>
</template>

<script>
import { EyeIcon } from '@heroicons/vue/outline';
import TimelineItem from '../../Timelines/TimelineItem.vue';

export default {
    components: {
        EyeIcon,
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