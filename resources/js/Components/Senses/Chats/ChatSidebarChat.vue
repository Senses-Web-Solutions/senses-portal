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
                <h4 class="text-black font-semibold">
                    {{ chat.name }}
                </h4>
                <p class="text-zinc-400 text-sm">
                    {{
                        FormatTime(chat?.last_message?.sent_at, "HH:mm") ??
                        FormatTime(new Date.now(), "HH:mm")
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
                    v-if="chat.unread_messages_count > 0"
                    class="h-5 w-5 bg-primary-400 rounded-full flex justify-center items-center text-white text-xs"
                >
                    {{ chat.unread_messages_count ?? 1 }}
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import FormatTime from "../../../Filters/FormatTime";

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
                // return [...this.chat.typers].join(", ") + " " + (this.chat.typers.size > 1 ? "are" : "is") + " typing...";
                return "Typing...";
            }

            const content = this.chat?.last_message?.content ?? "No messages";
            const imgCount = (content.match(/<img /g) || []).length;

            if (imgCount > 0) {
                return ' (' + imgCount + ' image' + (imgCount > 1 ? 's' : '') + ')';
            }

            return content;
        },
    },
    methods: {
        FormatTime,
        selectChat(chat) {
            this.$emit("select-chat", chat);
        },
    },
}
</script>