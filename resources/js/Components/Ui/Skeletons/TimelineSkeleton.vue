<template>
    <div>
        <div class="flex justify-between items-center space-x-3 p-5" v-for="i in items - 1" :key="`timelineSkeletonItem${i}`">
            <span class="py-2 px-2 rounded-full border shadow-sm font-medium border-transparent bg-zinc-200">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>

            <div class="flex-1">
                <div class="space-y-2">
                    <div class="h-4 bg-zinc-200 rounded-lg"></div>
                    <div class="h-4 w-64 bg-zinc-200 rounded-lg"></div>
                </div>
            </div>

        </div>
    </div>
</template>
<script>
export default {
    props: {
        items: {
            type: Number,
            default: 4
        }
    },
}
</script>
