<template>
    <div class="flex flex-col justify-between flex-grow">
        <div class="p-3 border-b border-zinc-200 flex items-center justify-between">
            <div v-if="!cobrowsing" class="text-black font-semibold text-xl">
                {{ chat.name }}
            </div>
            <div v-else class="text-black font-semibold text-xl">
                Chat
            </div>

            <div class="flex items-center">
                <ChatAgents :agents="chat.agents" />
                <ButtonGroup>
                    <ChatActions :chat="chat" :show-history="showHistory" :cobrowsing="cobrowsing" />
                </ButtonGroup>
            </div>
        </div>


        <div id="messages" class="flex-grow overflow-y-auto overflow-x-hidden p-3 relative bg-white">
            <TransitionGroup
                enter-active-class="transition duration-150 ease-out"
                enter-from-class="scale-75 opacity-0"
                enter-to-class="scale-100 opacity-100"
                leave-active-class="transition duration-0 ease-in"
                leave-from-class="scale-100 opacity-100"
                leave-to-class="scale-75 opacity-0"
            >
                <Message
                    v-for="(message, index) in chat?.messages"
                    :key="'message:' + message.id"
                    :message="message"
                    :in-chain="isInChain(message, index)"
                    :your-assigned="yourAssigned"
                />
            </TransitionGroup>
        </div>


        <div v-if="chat?.typers?.size > 0" class="transition duration-200 text-black px-3 py-1 border-t border-zinc-200 text-zinc-400" :class="{ 'opacity-0' : !chat?.typers.size }">
            {{ [...chat?.typers].join(', ') }} {{ chat?.typers.size > 1 ? 'are' : 'is' }} typing...
        </div>


        <ChatInput :chat="chat" :your-assigned="yourAssigned" />
    </div>
</template>

<script>

import Message from '../Messages/Message.vue';
import SeInput from '../../Ui/Inputs/SeInput.vue';

import ButtonGroup from '../../Ui/Buttons/ButtonGroup.vue';

import ChatAgents from './ChatAgents.vue';
import ChatActions from './ChatActions.vue';
import ChatInput from './ChatInput.vue';

import user from '../../../Support/user';

export default {
    components: {
        Message,
        SeInput,
        ButtonGroup,
        ChatAgents,
        ChatActions,
        ChatInput
    },
    props: {
        chat: {
            type: [Object, null],
            required: true
        },
        showHistory: {
            type: Boolean,
            default: false
        },
        cobrowsing: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {

        }
    },
    computed: {
        unassigned() {
            return this.chat?.agents?.length === 0 || !this.chat?.agents;
        },
        yourAssigned() {
            return this.chat?.agents?.some(agent => agent.id === user().id) ?? false;
        },
    },

    watch: {
        typers() {
            this.chat.typers = this.typers;
        }
    },

    methods: {
        isInChain(message) {
            let messages = Object.values(this.chat.messages);
            // Find index of message in array of messages
            const index = messages.findIndex(m => m.id === message.id);

            if (messages.length <= 1 || index === 0) {
                return false;
            }

            let lastMessage = messages[index - 1];
            return message.author === lastMessage.author;
        },
    }
}
</script>