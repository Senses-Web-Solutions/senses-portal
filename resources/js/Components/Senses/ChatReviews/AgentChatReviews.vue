<template>
    <AgentChatReviewStats :id="id" />
    <AgentChatReviewTable :id="id" />
</template>
<script>
import AgentChatReviewStats from './AgentChatReviewStats.vue';
import AgentChatReviewTable from './AgentChatReviewTable.vue';

export default {
    components: {
        AgentChatReviewStats,
        AgentChatReviewTable,
    },
    props: {
        id: {
            type: Number,
            required: false,
        },
    }
}
</script>