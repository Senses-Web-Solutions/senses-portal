<template>
    <div v-if="chats?.length > 0" class="border-t border-zinc-200 pt-3">
        <div class="flex justify-between">
            <h2 class="px-3 text-black font-semibold text-lg capitalize mb-1">
                {{ type }}
            </h2>
            <h2 class="px-3 text-black font-semibold text-lg capitalize mb-1">
                {{ chats.length }}
            </h2>
        </div>
        <div class="max-h-1/4 overflow-y-scroll hide-scrollbar">
            <TransitionGroup
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition duration-0 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <ChatSidebarChat
                    v-for="chat in chats"
                    :key="chat.id"
                    :chat="chat"
                    :selected-chat="selectedChat"
                    @select-chat="selectChat"
                />
            </TransitionGroup>
            <EmptyState v-if="chats?.length === 0">
                {{ emptyMessage }}
            </EmptyState>
        </div>
    </div>
</template>
<script>
import EmptyState from "../../Ui/EmptyState.vue";

import ChatSidebarChat from "./ChatSidebarChat.vue";

export default {
    components: {
        EmptyState,
        ChatSidebarChat,
    },
    props: {
        chats: {
            type: Array,
            required: true,
        },
        selectedChat: {
            type: Object,
            required: false,
            default: null,
        },
        type: {
            type: String,
            required: true,
        },
        emptyMessage: {
            type: String,
            required: false,
            default: "No chats found",
        },
    },
    emits: ["select-chat"],
    methods: {
        selectChat(chat) {
            this.$emit("select-chat", chat);
        },
    },
};
</script>
<style>
.hide-scrollbar::-webkit-scrollbar {
    display: none;
}

.hide-scrollbar {
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
}
</style>