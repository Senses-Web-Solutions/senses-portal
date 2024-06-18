<template>
    <div class="p-4 border-l border-zinc-200 w-96 overflow-y-scroll">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-xl font-bold text-black">History</h1>
            <SecondaryButton @click="hide">Hide</SecondaryButton>
        </div>

        <div v-if="chat.action_logs.length > 0" class="space-y-4">
            <ChatActionLog
                v-for="(actionLog, index) in chat.action_logs"
                :key="actionLog.id"
                :action-log="actionLog"
                :last="index === chat.action_logs.length - 1"
            />
        </div>
    </div>
</template>
<script>
import ChatActionLog from '../ActionLogs/Chats/ChatActionLog.vue';
import SecondaryButton from '../../Ui/Buttons/SecondaryButton.vue';

import EventHub from '../../../Support/EventHub';

export default {
    components: {
        ChatActionLog,
        SecondaryButton
    },
    props: {
        chat: {
            type: Object,
            required: true
        }
    },
    methods: {
        hide() {
            EventHub.emit('chats:hide-history');
        }
    },
}
</script>