<template>
    <CreatedChatActionLog v-if="created" :action-log="actionLog" :last="last" />
    <JoinedChatActionLog v-else-if="joined" :action-log="actionLog" :last="last" />
    <LeftChatActionLog v-else-if="left" :action-log="actionLog" :last="last"/>
    <SentInviteActionLog v-else-if="invited" :action-log="actionLog" :last="last"/>
    <InviteAcceptedActionLog v-else-if="acceptedInvite" :action-log="actionLog" :last="last"/>
    <InviteRejectedActionLog v-else-if="rejectedInvite" :action-log="actionLog" :last="last"/>
    <RequestCobrowseActionLog v-else-if="requestedCobrowse" :action-log="actionLog" :last="last"/>
    <AcceptedCobrowseActionLog v-else-if="acceptedCobrowse" :action-log="actionLog" :last="last"/>
    <StoppedCobrowseActionLog v-else-if="stoppedCobrowse" :action-log="actionLog" :last="last"/>
</template>
<script>
import TimelineItem from '../../Timelines/TimelineItem.vue';
import CreatedChatActionLog from './CreatedChatActionLog.vue';
import JoinedChatActionLog from './JoinedChatActionLog.vue';
import LeftChatActionLog from './LeftChatActionLog.vue';
import SentInviteActionLog from './SentInviteActionLog.vue';
import InviteAcceptedActionLog from './InviteAcceptedActionLog.vue';
import InviteRejectedActionLog from './InviteRejectedActionLog.vue';
import RequestCobrowseActionLog from './RequestCobrowseActionLog.vue';
import AcceptedCobrowseActionLog from './AcceptedCobrowseActionLog.vue';
import StoppedCobrowseActionLog from './StoppedCobrowseActionLog.vue';

export default {
    components: {
        TimelineItem,
        CreatedChatActionLog,
        JoinedChatActionLog,
        LeftChatActionLog,
        SentInviteActionLog,
        InviteAcceptedActionLog,
        InviteRejectedActionLog,
        RequestCobrowseActionLog,
        AcceptedCobrowseActionLog,
        StoppedCobrowseActionLog
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
        },
        requestedCobrowse() {
            return this.actionLog.type === 'request-cobrowse';
        },
        acceptedCobrowse() {
            return this.actionLog.type === 'accepted-cobrowse';
        },
        stoppedCobrowse() {
            return this.actionLog.type === 'stopped-cobrowse';
        }
    }
}
</script>