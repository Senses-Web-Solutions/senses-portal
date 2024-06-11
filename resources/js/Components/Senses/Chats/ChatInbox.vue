<template>
    <!-- Inbox has three columns -->
    <div class="flex w-full h-full" style="max-height: calc(100vh - 132px); min-height: calc(100vh - 132px)">
        <ChatSidebar :chats="formattedChats" :selected-chat="selectedChat" :loading-chats="loadingChats" @chatSelected="(chat) => selectedChat = chat"/>

        <ChatMessenger v-if="selectedChat" :chat="selectedChat" />
        <div v-else class="h-full w-full flex items-center justify-center text-black" style="min-height: calc(100vh - 132px)">
            <h2 class="w-max text-xl">Select a chat on the sidebar</h2>
        </div>
    </div>
</template>
<script>
import axios from 'axios';

import ChatSidebar from './ChatSidebar.vue';
import ChatMessenger from './ChatMessenger.vue';

import EventHub from '../../../Support/EventHub';
import useEcho from '../../../Support/useEcho';
import user from '../../../Support/user';

const echo = useEcho();

export default {
    components: {
        ChatSidebar,
        ChatMessenger,
    },
    props: {
        url: {
            type: String,
            default: '/api/v2/inbox/chats'
        }
    },
    data() {
        return {
            chats: {},

            selectedChat: null,
            loadingChats: true,
            user: user(),
        }
    },
    computed: {
        formattedChats() {
            if (Object.values(this.chats).length === 0) {
                return {
                    'new': [],
                    'assigned': [],
                };
            }
            return Object.values(this.chats).reduce((acc, chat) => {
                if (!acc[chat.status.slug]) {
                    acc[chat.status.slug] = [];
                }

                acc[chat.status.slug].push(chat);

                // Sort chats by sent_at in descending order
                acc[chat.status.slug].sort((a, b) => {
                    return b.id - a.id;
                });

                return acc;
            }, {});
        }
    },
    mounted() {
        this.fetchChats();
        this.setupEchoListeners();

        EventHub.on('chats:fetch', this.fetchChats)
    },
    beforeUnmount() {
        EventHub.off('chats:fetch')

        this.destroyEchoListeners();
    },
    methods: {
        fetchChats() {
            this.loadingChats = true;

            axios.get(this.url)
                .then(response => {
                    this.chats = response.data;
                    // this.formatChats();
                })
                .catch(error => {
                    console.log(error)
                })
                .finally(() => {
                    this.loadingChats = false;
                })
        },

        setupEchoListeners() {
            echo.private(`companies.${this.user.company_id}.chat`).listen('Chats\\ChatCreated', ({chat}) => {
                chat.messages = {};

                // Add chat to chats
                this.createOrUpdateChat(chat);
            })

            echo.private(`companies.${this.user.company_id}.message`).listen('Messages\\MessageCreated', ({message}) => {
                // Find chat in chats, update last message and unread messages count and add to messages
                this.createOrUpdateMessage(message);
            })

            echo.private(`companies.${this.user.company_id}.message`).listen('Messages\\MessageUpdated', ({message}) => {
                // Find chat in chats, update last message and unread messages count and add to messages
                this.createOrUpdateMessage(message);
            })

            echo.private(`companies.${this.user.company_id}.message`).listen('Chats\\Typing', (data) => {
                if (data.from_agent) {
                    return;
                }

                // If typers does not exist on chat, create it
                if (!this.chats[data.chat.id].typers) {
                    this.chats[data.chat.id].typers = new Set();
                }

                this.addTyper(data.chat.id, data.name);
            })

            echo.private(`companies.${this.user.company_id}.message`).listen('Chats\\StopTyping', (data) => {
                if (data.from_agent) {
                    return;
                }

                this.removeTyper(data.chat.id, data.name);
            })
        },

        destroyEchoListeners() {
            echo.leave(`companies.${this.user.company_id}.chat`);
            echo.leave(`companies.${this.user.company_id}.message`);
        },


        createOrUpdateChat(chat) {
            this.chats[chat.id] = chat;
        },

        createOrUpdateMessage(message) {
            const chat = this.chats[message.chat_id];
            
            if (message.read_at) {
                chat.unread_messages_count -= 1;
                } else {
                chat.last_message = message;
                chat.unread_messages_count += 1;
            }

            chat.messages[message.id] = message;
        },

        addTyper(chatId, name) {
            this.chats[chatId].typers.add(name);
        },

        removeTyper(chatId, name) {
            this.chats[chatId].typers.delete(name);
        }
    }
}
</script>