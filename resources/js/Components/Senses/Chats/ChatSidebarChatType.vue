<template>
    <div v-if="chats?.length > 0" class="border-t border-zinc-200 pt-3">
        <div class="flex justify-between">
            <h2 class="px-4 text-black font-semibold text-lg capitalize mb-1">
                {{ type }}
            </h2>
            <h2 class="px-4 text-black font-semibold text-lg capitalize mb-1">
                {{ chats.length }}
            </h2>
        </div>
        <div class="max-h-1/4 overflow-y-scroll hide-scrollbar">
            <div
                v-for="chat in chats"
                :key="chat.id"
                class="flex gap-2 p-4 cursor-pointer transition-all border border-transparent hover:bg-zinc-200"
                :class="{
                    'bg-zinc-200 !border !border-primary-400':
                        selectedChat && selectedChat.id === chat.id,
                }"
                @click="selectChat(chat)"
            >
                <div
                    class="block h-8 w-8 rounded-full bg-primary-400 flex justify-center items-center text-white shrink-0"
                >
                    JD
                </div>
    
                <div class="w-full">
                    <div class="flex justify-between mb-1 w-full">
                        <h4 class="text-black font-semibold">
                            {{ chat.name }}
                        </h4>
                        <p class="text-zinc-400 text-sm">{{ FormatTime(chat?.last_message?.sent_at, 'HH:mm') ?? FormatTime(new Date.now(), 'HH:mm') }}</p>
                    </div>
                    <div class="flex justify-between w-full">
                        <p class="text-zinc-500">
                            {{ truncateMessage(chat?.last_message?.content) ?? '...' }}
                        </p>
                        <div
                            v-if="chat.unread_messages_count > 0"
                            class="h-5 w-5 bg-primary-400 rounded-full flex justify-center items-center text-white text-xs"
                        >
                            {{ chat.unread_messages_count ?? 1 }}
                        </div>
                    </div>
                </div>
            </div>
            <EmptyState v-if="chats?.length === 0">
                {{ emptyMessage }}
            </EmptyState>
        </div>
    </div>
</template>
<script>
import EmptyState from "../../Ui/EmptyState.vue";
import FormatTime from "../../../Filters/FormatTime";

export default {
    components: {
        EmptyState,
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
        truncateMessage(message) {
            if (!message) {
                return "...";
            }
            return message.length > 50
                ? message.substring(0, 50) + "..."
                : message;
        },
        FormatTime,
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