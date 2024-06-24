<template>
    <TimelineItem :datetime="actionLog.logged_at" :last="last">
        <template #icon>
            <CheckIcon class="h-4 w-4 text-green-500" />
        </template>
        <template #title>
            <span class="text-black">{{ agent }}</span> accepted the invite
        </template>
    </TimelineItem>
</template>

<script>
import { CheckIcon } from '@heroicons/vue/outline';
import TimelineItem from '../../Timelines/TimelineItem.vue';

export default {
    components: {
        CheckIcon,
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
        invitee() {
            return this.actionLog.data.invited_user;
        },
    },
}
</script>