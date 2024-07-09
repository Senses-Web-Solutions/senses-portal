<template>
    <div class="border-zinc-200 overflow-y-scroll transition-width duration-500" :class="showClasses">
        <div class="flex items-center justify-between p-3 border-b border-zinc-200" v-if="show">
            <h1 class="text-xl font-bold text-black">History</h1>
            <SecondaryButton @click="hide">Hide</SecondaryButton>
        </div>

        <div class="w-full h-full relative bg-white p-3">
            <div v-if="actionLogs.length > 0 && show" class="space-y-4">
                <ChatActionLog
                    v-for="(actionLog, index) in actionLogs"
                    :key="actionLog.id"
                    :action-log="actionLog"
                    :last="index === actionLogs.length - 1"
                />
            </div>
    
            <EmptyState v-else>
                No history found
            </EmptyState>
        </div>

    </div>
</template>
<script>
import axios from 'axios';
import ChatActionLog from '../ActionLogs/Chats/ChatActionLog.vue';
import SecondaryButton from '../../Ui/Buttons/SecondaryButton.vue';
import EmptyState from '../../Ui/EmptyState.vue';

import EventHub from '../../../Support/EventHub';

export default {
    components: {
        ChatActionLog,
        SecondaryButton,
        EmptyState
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
    data() {
        return {
            actionLogs: []
        }
    },
    computed: {
        showClasses() {
            return {
                'w-0 translate-x-full p-0': !this.show,
                'w-96 border-l': this.show,
            }
        },
    },
    watch: {
        show(value) {
            if (value) {
                this.fetchHistory();
            }
        }
    },
    methods: {
        hide() {
            EventHub.emit('chats:hide-history');
        },

        fetchHistory() {
            console.log('Fetch history');
            axios.get(`/api/v2/chats/${this.chat.id}/action-logs`)
                .then(response => {
                    response.data.data.sort((a, b) => a.id - b.id);
                    this.actionLogs = response.data.data;
                })
                .catch(error => {
                    console.error(error);
                });
        }
    },
}
</script>
<style>
.transition-width {
    transition-property: width;
}
</style>