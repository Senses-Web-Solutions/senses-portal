<template>
    <!-- Inbox has three columns -->
    <div
        class="flex w-full h-full overflow-x-hidden"
        style="max-height: calc(100vh - 128px); min-height: calc(100vh - 128px)"
    >
        <ChatSidebar
            :chats="formattedChats"
            :selected-chat="selectedChat"
            :loading-chats="loadingChats"
            @chatSelected="(chat) => (selectedChat = chat)"
        />

        <ChatCobrowse v-if="cobrowsing" :chat="selectedChat" :cobrowsing="cobrowsing" />
        <Chat 
            v-if="selectedChat" 
            :chat="selectedChat" 
            :show-history="showHistory" 
            :show-details="showDetails" 
            :cobrowsing="cobrowsing" 
        />
        <ChatDetails :chat="selectedChat" :show="showDetails" />
        <ChatActionLogs :chat="selectedChat" :show="showHistory" />
    </div>
</template>
<script>
import axios from "axios";
import { Howl, Howler } from "howler";

import Chat from "./Chat.vue";
import ChatSidebar from "./ChatSidebar.vue";
import ChatCobrowse from "./ChatCobrowse.vue";
import ChatActionLogs from "./ChatActionLogs.vue";
import ChatDetails from "./ChatDetails.vue";

import EventHub from "../../../Support/EventHub";
import useEcho from "../../../Support/useEcho";
import user from "../../../Support/user";

const echo = useEcho();

export default {
    components: {
        ChatSidebar,
        Chat,
        ChatCobrowse,
        ChatActionLogs,
        ChatDetails,
    },
    props: {
        url: {
            type: String,
            default: "/api/v2/inbox/chats",
        },
    },
    data() {
        return {
            chats: {},

            selectedChat: null,
            showHistory: false,
            showDetails: false,
            loadingChats: true,

            chatSoundPlaying: false,

            chatSound: new Howl({
                src: ["/sounds/notification-2.mp3"],
                volume: 1,
                loop: true,
                rate: 0.75
            }),

            messageSound: new Howl({
                src: ["/sounds/notification-1.mp3"],
                volume: 1,
            }),

            originalTitle: document.title,

            cobrowsing: false,
            chatChannel: null,
            messageChannel: null,
        };
    },
    computed: {
        chatArray() {
            return Object.values(this.chats);
        },
        formattedChats() {
            if (this.chatArray.length === 0) {
                return {
                    new: [],
                    assigned: [],
                    unassigned: [],
                    "in progress": [],
                    invited: [],
                };
            }
            return this.chatArray.reduce((acc, chat) => {
                let slug = chat.status.slug;

                if (chat?.invited_agents.some((agent) => agent.id === user().id)) {
                    slug = "invited";
                }

                // Check if the chat status is 'assigned' and the user is not an agent
                if (
                    slug === "assigned" &&
                    !chat.agents.some((agent) => agent.id === user().id)
                ) {
                    slug = "in progress";
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
        },
    },
    watch: {
        chats: {
            handler() {
                // If all chats have agents, stop playing the chat sound
                if (this.chatArray.every((chat) => chat?.agents?.length > 0)) {
                    this.chatSound.stop();
                    this.chatSoundPlaying = false;
                }
            },
            deep: true,
        },
    },
    mounted() {
        this.setupAudio();
        this.fetchChats();
        this.setupEchoListeners();
        this.setupEventHubListeners();
    },
    beforeUnmount() {
        this.destroyAudio();
        this.destroyEchoListeners();
        this.destroyEventHubListeners();
    },
    methods: {
        setupEventHubListeners() {
            // EventHub.on("chats:join", this.chatJoined);
            // EventHub.on("chats:leave", this.chatLeft);
            EventHub.on("chats:delete", this.chatDeleted);
            EventHub.on("chats:complete", this.chatCompleted)
            EventHub.on("chats:fetch", this.fetchChats);
            EventHub.on("chats:show-history", () => (this.showHistory = true));
            EventHub.on("chats:hide-history", () => (this.showHistory = false));
            EventHub.on("chats:show-details", () => (this.showDetails = true));
            EventHub.on("chats:hide-details", () => (this.showDetails = false));
            EventHub.on('cobrowse:stop', () => {this.cobrowsing = false});
        },
        destroyEventHubListeners() {
            EventHub.off("chats:join");
            EventHub.off("chats:leave");
            EventHub.off("chats:fetch");
            EventHub.off("chats:show-history");
            EventHub.off("chats:hide-history");
            EventHub.off("chats:show-details");
            EventHub.off("chats:hide-details");
            EventHub.off('cobrowse:stop');
        },
        chatDeleted(id) {
            delete this.chats[id];
            this.selectedChat = null;
        },
        chatCompleted(id) {
            delete this.chats[id];
            this.selectedChat = null;
        },
        setupAudio() {
            // Create audio context
            let handleMouseMove = () => {
                if (Howler.ctx.state !== "running") {
                    Howler.ctx.resume();
                    document.documentElement.removeEventListener(
                        "mousemove",
                        handleMouseMove
                    );
                }
            };

            document.documentElement.addEventListener(
                "mousemove",
                handleMouseMove
            );
        },
        destroyAudio() {
            Howler.unload();
        },
        fetchChats() {
            this.loadingChats = true;

            axios
                .get(this.url)
                .then((response) => {
                    if (response.data === []) {
                        return;
                    }

                    this.chats = response.data;
                })
                .catch((error) => {
                    console.log(error);
                })
                .finally(() => {
                    this.loadingChats = false;

                    // If any of the chats have no agents, play the chat sound
                    if (
                        this.chatArray.some((chat) => chat.agents.length === 0)
                    ) {
                        this.chatSound.play();
                        this.chatSoundPlaying = true;
                    }
                });
        },

        createNotification(chat) {

            if (!("Notification" in window)) {
                console.log(
                    "This browser does not support desktop notification"
                );
            } else if (Notification.permission === "granted") {
                let notification = new Notification(`New Chat - ${chat.name}`, {
                    body: `New Chat - ${chat.name}`,
                });
            } else if (Notification.permission === "denied") {
                Notification.requestPermission().then(function (permission) {
                    if (permission === "granted") {
                        let notification = new Notification(
                            `New Chat - ${chat.name}`,
                            {
                                body: `New Chat - ${chat.name}`,
                            }
                        );
                    }
                });
            }
        },

        setupEchoListeners() {
            const chatChannel = echo.private(`companies.${user().company_id}.chat`);
            const messageChannel = echo.private(`companies.${user().company_id}.message`);

            chatChannel.listen(
                "Chats\\ChatCreated",
                ({ chat }) => {
                    chat.messages = {};
                    if (!this.chatSoundPlaying) {
                        this.chatSound.play();
                        this.chatSoundPlaying = true;
                    }

                    // Add chat to chats
                    this.createOrUpdateChat(chat);

                    // Create a notification
                    this.createNotification(chat);
                }
            );

            chatChannel.listen(
                "Chats\\ChatUpdated",
                ({ chat }) => {
                    // If chat has no agents, play the chat sound
                    if (chat.agents.length === 0) {
                        this.chatSound.play();
                        this.chatSoundPlaying = true;
                    }

                    // Add chat to chats
                    this.createOrUpdateChat(chat);
                }
            );

            messageChannel.listen(
                "Messages\\MessageCreated",
                ({ message }) => {
                    if (
                        message.author.full_name !== user().full_name &&
                        !this.chatSoundPlaying
                    ) {
                        this.messageSound.play();

                        // Edit page title
                        document.title = "New message!";
                        setTimeout(() => {
                            document.title = this.originalTitle;
                        }, 3000);
                    }

                    this.createOrUpdateMessage(message);
                }
            );

            messageChannel.listen(
                "Messages\\MessageUpdated",
                ({ message }) => {
                    this.createOrUpdateMessage(message);
                }
            );

            messageChannel.listen(
                "Chats\\Typing",
                (data) => {
                    if (data.from_agent) {
                        return;
                    }

                    // If typers does not exist on chat, create it
                    if (!this.chats[data.chat_id].typers) {
                        this.chats[data.chat_id].typers = new Set();
                    }

                    this.addTyper(data.chat_id, data.name);
                }
            );

            messageChannel.listen(
                "Chats\\StopTyping",
                (data) => {
                    if (data.from_agent) {
                        return;
                    }

                    this.removeTyper(data.chat_id, data.name);
                }
            );

            chatChannel.listen(
                "Chats\\StartCobrowse",
                (data) => {
                    this.cobrowsing = true;

                    this.$nextTick(() => {
                        EventHub.emit('cobrowse:start');
                    });
                }
            );

            this.chatChannel = chatChannel;
            this.messageChannel = messageChannel;
        },

        destroyEchoListeners() {
            // this.chatChannel.stopListening("Chats\\ChatCreated");
            // this.chatChannel.stopListening("Chats\\ChatUpdated");
            // this.messageChannel.stopListening("Messages\\MessageCreated");
            // this.messageChannel.stopListening("Messages\\MessageUpdated");
            // this.messageChannel.stopListening("Chats\\Typing");
            // this.messageChannel.stopListening("Chats\\StopTyping");

            // echo.leave(`companies.${user().company_id}.chat`);
            // echo.leave(`companies.${user().company_id}.message`);
        },

        createOrUpdateChat(chat) {
            this.chats[chat.id] = chat;

            // If there are no more chats without agents, stop playing the chat sound
            if (this.chatArray.every((chat) => chat?.agents?.length > 0)) {
                this.chatSound.stop();
                this.chatSoundPlaying = false;
            }

            if (this.selectedChat && this.selectedChat.id === chat.id) {
                this.selectedChat = chat;
            }
        },

        createOrUpdateMessage(message) {
            const chat = this.chats[message.chat_id];

            if (message.id > chat.last_message?.id || !chat.last_message) {
                chat.last_message = message;
            }

            chat.messages[message.id] = message;
        },

        addTyper(chatId, name) {
            this.chats[chatId].typers.add(name);
        },

        removeTyper(chatId, name) {
            if (this.chats[chatId]?.typers?.has(name)) {
                this.chats[chatId]?.typers?.delete(name);
            }
        },
    },
};
</script>
