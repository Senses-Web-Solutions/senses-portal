<template>
    <!-- Inbox has three columns -->
    <div class="flex w-full h-full" style="max-height: calc(100vh - 128px); min-height: calc(100vh - 128px)">
        <ChatSidebar :chats="formattedChats" :selected-chat="selectedChat" :loading-chats="loadingChats" @chatSelected="(chat) => selectedChat = chat"/>

        <Chat v-if="selectedChat" :chat="selectedChat" @chat-accepted="chatAccepted" />
        <div v-else class="h-full w-full flex items-center justify-center text-black" style="min-height: calc(100vh - 128px)">
            <h2 class="w-max text-xl">Select a chat on the sidebar</h2>
        </div>
    </div>
</template>
<script>
import axios from 'axios';
import {Howl, Howler} from 'howler';

import ChatSidebar from './ChatSidebar.vue';
import Chat from './Chat.vue';

import EventHub from '../../../Support/EventHub';
import useEcho from '../../../Support/useEcho';
import user from '../../../Support/user';

const echo = useEcho();

export default {
    components: {
        ChatSidebar,
        Chat,
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

            chatSoundPlaying: false,

            chatSound: new Howl({
                src: ['/sounds/notification-2.mp3'],
                volume: 1,
                loop: true,
            }),

            messageSound: new Howl({
                src: ['/sounds/notification-1.mp3'],
                volume: 1,
            }),

            originalTitle: document.title,
        }
    },
    computed: {
        formattedChats() {
            if (Object.values(this.chats).length === 0) {
                return {
                    'new': [],
                    'assigned': [],
                    'in progress': [],
                };
            }
            return Object.values(this.chats).reduce((acc, chat) => {
                let slug = chat.status.slug;

                // Check if the chat status is 'assigned' and the user is not an agent
                if (slug === 'assigned' && !chat.agents.some(agent => agent.id === this.user.id)) {
                    slug = 'in progress';
                }

                if (!acc[slug]) {
                    acc[slug] = [];
                }

                acc[slug].push(chat);

                // Sort chats by sent_at in descending order
                acc[slug].sort((a, b) => {
                    return b.id - a.id;
                });

                return acc;
            }, {});
        }
    },
    watch: {
        'chats': {
            handler() {
                // If all chats have agents, stop playing the chat sound
                if (Object.values(this.chats).every(chat => chat?.agents?.length > 0)) {
                    this.chatSound.stop();
                    this.chatSoundPlaying = false;
                }
            },
            deep: true
        }
    },
    mounted() {
        this.setupAudio();
        this.fetchChats();
        this.setupEchoListeners();

        EventHub.on('chats:fetch', this.fetchChats)
    },
    beforeUnmount() {
        this.destroyAudio();
        this.destroyEchoListeners();
        EventHub.off('chats:fetch')
    },
    methods: {
        setupAudio() {
            // Create audio context
            let handleMouseMove = () => {
                if (Howler.ctx.state !== 'running') {
                    Howler.ctx.resume();
                    document.documentElement.removeEventListener('mousemove', handleMouseMove);
                }
            };

            document.documentElement.addEventListener('mousemove', handleMouseMove);
        }, 
        destroyAudio() {
            Howler.unload();
        },
        fetchChats() {
            this.loadingChats = true;

            axios.get(this.url)
                .then(response => {
                    this.chats = response.data;
                })
                .catch(error => {
                    console.log(error)
                })
                .finally(() => {
                    this.loadingChats = false;

                    // If any of the chats have no agents, play the chat sound
                    if (Object.values(this.chats).some(chat => chat.agents.length === 0)) {
                        this.chatSound.play();
                        this.chatSoundPlaying = true;
                    }
                })
        },

        setupEchoListeners() {
            echo.private(`companies.${this.user.company_id}.chat`).listen('Chats\\ChatCreated', ({chat}) => {
                chat.messages = {};
                if (!this.chatSoundPlaying) {
                    this.chatSound.play();
                    this.chatSoundPlaying = true;
                }
                
                // Add chat to chats
                this.createOrUpdateChat(chat);
            })

            echo.private(`companies.${this.user.company_id}.message`).listen('Messages\\MessageCreated', ({message}) => {
                if (message.author !== this.user.full_name && !this.chatSoundPlaying) {
                    this.messageSound.play();
    
                    // Edit page title
                    document.title = 'New message!';
                    setTimeout(() => {
                        document.title = this.originalTitle;
                    }, 3000);
                }

                this.createOrUpdateMessage(message);
            })

            echo.private(`companies.${this.user.company_id}.message`).listen('Messages\\MessageUpdated', ({message}) => {
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
        },

        chatAccepted(chat) {
            this.chats[chat.id] = chat;

            // If there are no more chats without agents, stop playing the chat sound
            if (Object.values(this.chats).every(chat => chat?.agents?.length > 0)) {
                this.chatSound.stop();
                this.chatSoundPlaying = false;
            }
        }
    }
}
</script>