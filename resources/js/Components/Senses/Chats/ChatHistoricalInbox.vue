<template>
    <!-- Inbox has three columns -->
    <div
        class="flex w-full h-full overflow-x-hidden"
        style="max-height: calc(100vh - 128px); min-height: calc(100vh - 128px)"
    >
        <ChatHistoricalSidebar
            :chats="chatArray"
            :selected-chat="selectedChat"
            :loading-chats="loadingChats"
            @chatSelected="(chat) => (selectedChat = chat)"
        />

        <ChatCobrowse v-if="cobrowsing" :chat="selectedChat" :cobrowsing="cobrowsing" />
        <Chat 
            v-if="selectedChat" 
            :chat="selectedChat" 
            :show-history="showHistory" 
            :cobrowsing="cobrowsing" 
            :show-input="false" 
            :historical="true"
        />
        <ChatDetails :chat="selectedChat" :show="showDetails" />
        <ChatActionLogs :chat="selectedChat" :show="showHistory" />
    </div>
</template>
<script>
import axios from "axios";

import ChatHistoricalSidebar from "./ChatHistoricalSidebar.vue";
import ChatActionLogs from "./ChatActionLogs.vue";
import ChatDetails from "./ChatDetails.vue";
import Chat from "./Chat.vue";

import EventHub from "../../../Support/EventHub";
import useEcho from "../../../Support/useEcho";
import user from "../../../Support/user";
import ChatCobrowse from "./ChatCobrowse.vue";

const echo = useEcho();

export default {
    components: {
        ChatHistoricalSidebar,
        Chat,
        ChatCobrowse,
        ChatActionLogs,
        ChatDetails
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

            originalTitle: document.title,

            cobrowsing: false,
        };
    },
    computed: {
        chatArray() {
            return Object.values(this.chats).sort((a, b) => {
                return a.updated_at < b.updated_at ? 1 : -1;
            });
        },
    },
    mounted() {
        this.fetchChats();
        this.setupEchoListeners();
        this.setupEventHubListeners();
    },
    beforeUnmount() {
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
                    // Add chat to chats
                    this.createOrUpdateChat(chat);

                    // Create a notification
                    this.createNotification(chat);
                }
            );

            chatChannel.listen(
                "Chats\\ChatUpdated",
                ({ chat }) => {

                    // Add chat to chats
                    this.createOrUpdateChat(chat);
                }
            );

            messageChannel.listen(
                "Messages\\MessageCreated",
                ({ message }) => {
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
                ({data}) => {
                    this.cobrowsing = true;

                    this.$nextTick(() => {
                        EventHub.emit('cobrowse:start');
                    });
                }
            );
        },

        destroyEchoListeners() {
            echo.leave(`companies.${user().company_id}.chat`);
            echo.leave(`companies.${user().company_id}.message`);
        },

        createOrUpdateChat(chat) {
            this.chats[chat.id] = chat;

            if (this.selectedChat && this.selectedChat.id === chat.id) {
                this.selectedChat = chat;
            }
        },

        createOrUpdateMessage(message) {
            console.log(message);
            console.log(this.chats);
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
            this.chats[chatId].typers.delete(name);
        },
    },
};
</script>
