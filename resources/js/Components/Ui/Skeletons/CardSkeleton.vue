<template>
    <Card>
        <template #title>
            <div class="w-32 h-6 bg-zinc-200 rounded-lg"></div>
        </template>
        <template #description>
            <div class="h-3 mt-1 bg-zinc-200 rounded-lg w-72"></div>
        </template>
        <template #actions>
            <span class="px-4 py-2 font-medium bg-zinc-200 border border-transparent rounded-lg shadow-sm">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
        </template>

        <div class="space-y-2">
            <div v-for="i in items - 1" :key="`cardSkeletonItem${i}`" class="h-4 bg-zinc-200 rounded-lg"></div>
            <div class="w-64 h-4 bg-zinc-200 rounded-lg"></div>
        </div>
    </Card>
</template>
<script>
import Card from '../Cards/Card.vue';
export default {
    props: {
        items: {
            type: Number,
            default: 4
        }
    },
    components: { Card }
}
</script>
