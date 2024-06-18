<template>
    <TimelineItem :datetime="actionLog.logged_at">
        <template #icon>
            <XIcon class="h-4 w-4 text-red-500" />
        </template>
        <template #title>
            <span class="text-black">{{ agent }}</span> rejected the invite
        </template>
    </TimelineItem>
</template>

<script>
import { XIcon } from '@heroicons/vue/outline';
import TimelineItem from '../../Timelines/TimelineItem.vue';

export default {
    components: {
        XIcon,
        TimelineItem
    },
    props: {
        actionLog: {
            type: Object,
            required: true
        },
    },
    computed: {
        agent() {
            return this.actionLog.user.full_name;
        },
        invitee() {
            return this.actionLog.data.invited_user;
        },
    },
}
</script>