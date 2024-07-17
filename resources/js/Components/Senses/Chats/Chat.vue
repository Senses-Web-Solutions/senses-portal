<template>
    <div class="flex flex-col justify-between flex-grow">
        <div class="p-3 border-b border-zinc-200 flex items-center justify-between bg-white">
            <div v-if="!cobrowsing" class="flex flex-col text-black font-semibold text-xl space-y-1">
                <div class="flex items-center text-sm text-zinc-400 hover:text-primary-500 transition-all">
                    <a :href="chat?.system" target="_blank">{{ chat?.current_page }}<ExternalLinkIcon class="h-4 w-4 inline-block" /></a>
                </div>
                <p class="text-sm">{{ chat?.chat_user?.full_name }}</p> 
            </div>
            <div v-else class="text-black font-semibold text-xl">
                Chat
            </div>

            <div class="flex items-center">
                <ChatAgents :agents="chat.agents" />
                <ButtonGroup>
                    <ChatActions :chat="chat" :show-history="showHistory" :show-details="showDetails" :cobrowsing="cobrowsing" :historical="historical" />
                </ButtonGroup>
            </div>
        </div>

        <div id="messages" class="flex-grow overflow-y-auto overflow-x-hidden p-3 relative bg-zinc-100">
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


        <ChatInput v-if="showInput" :chat="chat" :your-assigned="yourAssigned" />
    </div>
</template>

<script>
import { ExternalLinkIcon } from '@heroicons/vue/outline';

import Message from '../Messages/Message.vue';
import SeInput from '../../Ui/Inputs/SeInput.vue';

import ButtonGroup from '../../Ui/Buttons/ButtonGroup.vue';

import ChatAgents from './ChatAgents.vue';
import ChatActions from './ChatActions.vue';
import ChatInput from './ChatInput.vue';

import user from '../../../Support/user';
import EventHub from '../../../Support/EventHub';

export default {
    components: {
        ExternalLinkIcon,
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
        showDetails: {
            type: Boolean,
            default: false
        },
        cobrowsing: {
            type: Boolean,
            default: false
        },
        showInput: {
            type: Boolean,
            default: true
        },
        historical: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            keydownListener: null
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

    mounted() {
        this.setupKeyboardListeners();
    },

    beforeUnmount() {
        this.destroyKeyboardListeners();
    },

    methods: {
        setupKeyboardListeners() {
            this.keydownListener = (event) => {
                if (event.ctrlKey) {
                    if (event.key === 'd') {
                        if (this.showDetails) {
                            EventHub.emit('chats:hide-details');
                        } else {
                            EventHub.emit('chats:show-details');
                        }
                    }

                    else if (event.key === 'h') {
                        if (this.showHistory) {
                            EventHub.emit('chats:hide-history');
                        } else {
                            EventHub.emit('chats:show-history');
                        }
                    }

                    event.preventDefault(); // Prevent default action to avoid any browser shortcut conflict
                }
            };
            document.addEventListener('keydown', this.keydownListener);
        },
        destroyKeyboardListeners() {
            document.removeEventListener('keydown', this.keydownListener);
        },
        isInChain(message) {
            let messages = Object.values(this.chat.messages);
            // Find index of message in array of messages
            const index = messages.findIndex(m => m.id === message.id);

            if (messages.length <= 1 || index === 0) {
                return false;
            }

            let lastMessage = messages[index - 1];
            return message.author.full_name === lastMessage.author.full_name;
        },
    }
}
</script>