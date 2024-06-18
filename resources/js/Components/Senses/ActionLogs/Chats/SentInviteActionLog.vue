<template>
    <TimelineItem :datetime="actionLog.logged_at">
        <template #icon>
            <MailIcon class="h-4 w-4 text-blue-500" />
        </template>
        <template #title>
            <span class="text-black">{{ agent }}</span> invited <span class="text-black">{{ invitee }}</span>
        </template>
    </TimelineItem>
</template>

<script>
import { MailIcon } from '@heroicons/vue/outline';
import TimelineItem from '../../Timelines/TimelineItem.vue';

export default {
    components: {
        MailIcon,
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