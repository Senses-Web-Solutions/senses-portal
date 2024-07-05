<template>
    <AgentChatReviewStats />
    <AgentChatReviewTable />
</template>
<script>
import AgentChatReviewStats from './AgentChatReviewStats.vue';
import AgentChatReviewTable from './AgentChatReviewTable.vue';

export default {
    components: {
        AgentChatReviewStats,
        AgentChatReviewTable,
    },
}
</script>