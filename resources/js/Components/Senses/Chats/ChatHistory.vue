<template>
    <div v-if="chat?.action_logs" class="border-l border-zinc-200 overflow-y-scroll transition-width duration-300" :class="showClasses">
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
            type: [Object, null],
            required: true
        },
        show: {
            type: Boolean,
            required: true
        }
    },
    computed: {
        showClasses() {
            return {
                'w-0 translate-x-full p-0': !this.show,
                'w-96 p-4': this.show,
            }
        },
    },
    methods: {
        hide() {
            EventHub.emit('chats:hide-history');
        }
    },
}
</script>
<style>
.transition-width {
    transition-property: width;
}
</style>