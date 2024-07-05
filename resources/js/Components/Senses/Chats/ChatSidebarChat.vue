<template>
    <div
        class="flex gap-2 p-4 cursor-pointer transition-all border border-transparent hover:bg-zinc-200"
        :class="{
            'bg-zinc-200 !border !border-primary-400':
                selectedChat && selectedChat.id === chat.id,
        }"
        @click="selectChat(chat)"
    >

        <div class="w-full">
            <div class="flex justify-between mb-1 w-full">
                <h4 class="text-black font-semibold truncate w-[55%]">
                    {{chat?.chat_user?.full_name ?? 'Visitor' }}
                </h4>
                <p class="text-zinc-400 text-sm">
                    {{
                        FormatDateTime(chat?.last_message?.sent_at, "dd/MM HH:mm") ??
                        FormatDateTime(new Date.now(), "dd/MM HH:mm")
                    }}
                </p>
            </div>
            <div class="flex justify-between w-full">
                <p
                    class="truncate w-[85%] text-zinc-500"
                    :class="{
                        '!text-primary-500': chat?.typers?.size > 0,
                    }"
                >
                    {{ chatOverview }}
                </p>
                <div
                    v-if="unreadMessageCount > 0"
                    class="h-5 w-5 bg-primary-400 rounded-full flex justify-center items-center text-white text-xs"
                >
                    {{ unreadMessageCount ?? 1 }}
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import FormatDateTime from "../../../Filters/FormatDateTime";
import user from "../../../Support/user";

export default {
    props: {
        chat: {
            type: Object,
            required: true,
        },
        selectedChat: {
            type: Object,
            required: false,
            default: null,
        },
    },
    emits: ["select-chat"],
    computed: {
        chatOverview() {
            if (this.chat?.typers?.size > 0) {
                return "Typing...";
            }

            const content = this.chat?.last_message?.content ?? "No messages";
            const imgCount = (content.match(/<img /g) || []).length;

            if (imgCount > 0) {
                return ' (' + imgCount + ' image' + (imgCount > 1 ? 's' : '') + ')';
            }

            return content;
        },
        yourAssigned() {
            return this.chat?.agents?.some(agent => agent.id === user().id) ?? false;
        },
        someoneIsAssigned() {
            return this.chat?.agents?.length > 0;
        },
        unreadMessageCount() {
            if (!this.yourAssigned && this.someoneIsAssigned) return 0;

            const messagesArray = Object.values(this.chat.messages ?? {}) ?? [];

            const count = messagesArray.reduce((acc, message) => {
                return (message.author?.full_name !== user()?.full_name && !message.read_at) ? acc + 1 : acc;
            }, 0);

            return count;
        }
    },
    methods: {
        FormatDateTime,
        selectChat(chat) {
            this.$emit("select-chat", chat);
        },
    },
}
</script>