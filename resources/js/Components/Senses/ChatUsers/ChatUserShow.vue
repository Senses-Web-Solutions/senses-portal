<template>
    <div v-if="data && id" class="flex flex-col space-y-8">
        <ChatUserChatsStats  :id="id" />
        <ChatUserChatsTable :chat-user="data" :id="id" />
        <ChatUserChatReviewStats :id="id" />
        <ChatUserChatReviewsTable :chat-user="data" :id="id" />
    </div>
</template>
<script>
import ChatUserChatsTable from '../Chats/ChatUserChatsTable.vue';
import ChatUserChatReviewStats from '../ChatReviews/ChatUserChatReviewStats.vue';
import ChatUserChatReviewsTable from '../ChatReviews/ChatUserChatReviewsTable.vue';
import ChatUserChatsStats from '../Chats/ChatUserChatsStats.vue';

export default {
    components: {
        ChatUserChatsStats,
        ChatUserChatsTable,
        ChatUserChatReviewStats,
        ChatUserChatReviewsTable,
    },
    props: {
        data: {
            type: Object,
            required: true,
        },
        id: {
            type: Number,
            required: true,
        },
    }
}
</script>