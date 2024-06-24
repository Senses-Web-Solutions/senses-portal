<template>
    <TimelineItem :datetime="actionLog.logged_at" :last="last">
        <template #icon>
            <UserAddIcon class="h-4 w-4 text-primary-500" />
        </template>
        <template #title>
            <span class="text-black">{{ agent }}</span> joined
        </template>
    </TimelineItem>
</template>

<script>
import { UserAddIcon } from '@heroicons/vue/outline';
import TimelineItem from '../../Timelines/TimelineItem.vue';

export default {
    components: {
        UserAddIcon,
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