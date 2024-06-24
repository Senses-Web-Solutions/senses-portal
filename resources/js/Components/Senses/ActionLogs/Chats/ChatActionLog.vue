<template>
    <CreatedChatActionLog v-if="created" :action-log="actionLog" :last="last" />
    <JoinedChatActionLog v-if="joined" :action-log="actionLog" :last="last" />
    <LeftChatActionLog v-if="left" :action-log="actionLog" :last="last"/>
    <SentInviteActionLog v-if="invited" :action-log="actionLog" :last="last"/>
    <InviteAcceptedActionLog v-if="acceptedInvite" :action-log="actionLog" :last="last"/>
    <InviteRejectedActionLog v-if="rejectedInvite" :action-log="actionLog" :last="last"/>
</template>
<script>
import TimelineItem from '../../Timelines/TimelineItem.vue';
import CreatedChatActionLog from './CreatedChatActionLog.vue';
import JoinedChatActionLog from './JoinedChatActionLog.vue';
import LeftChatActionLog from './LeftChatActionLog.vue';
import SentInviteActionLog from './SentInviteActionLog.vue';
import InviteAcceptedActionLog from './InviteAcceptedActionLog.vue';
import InviteRejectedActionLog from './InviteRejectedActionLog.vue';

export default {
    components: {
        TimelineItem,
        CreatedChatActionLog,
        JoinedChatActionLog,
        LeftChatActionLog,
        SentInviteActionLog,
        InviteAcceptedActionLog,
        InviteRejectedActionLog
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
        created() {
            return this.actionLog.type === 'created';
        },
        joined() {
            return this.actionLog.type === 'joined';
        },
        left() {
            return this.actionLog.type === 'left';
        },
        invited() {
            return this.actionLog.type === 'invited';
        },
        acceptedInvite() {
            return this.actionLog.type === 'accepted-invite';
        },
        rejectedInvite() {
            return this.actionLog.type === 'rejected-invite';
        }
    }
}
</script>