<template>
    <div class="relative flex flex-col space-y-2 py-2">
        <div class="flex justify-between">
            <h3 class="font-medium text-zinc-700">{{ transaction.message }}</h3>
            <XIcon class="cursor-pointer h-4 w-4 text-zinc-500" @click="$emit('remove')"/>
        </div>
        <div class="flex justify-between items-center">
            <span
                class="h-2 rounded-md bg-zinc-100 w-11/12"
            >
                <span
                    class="h-2 rounded-md block"
                    :class="transaction.progress === transaction.progress_total ? 'bg-green-500' : 'bg-primary-500' "
                    :style="{
                        width: (transaction.progress / transaction.progress_total) * 100 + '%'
                    }"
                ></span>
            </span>

            <p v-if="transaction.progress !== transaction.progress_total" class="text-sm text-zinc-500 ml-2">{{ ((transaction.progress / transaction.progress_total) * 100).toFixed(0) }}%</p>
            <DownloadIcon v-else class="h-4 w-4 text-zinc-500"/>
        </div>
    </div>
</template>
<script>
import { DownloadIcon } from "@heroicons/vue/outline";
import { XIcon } from "@heroicons/vue/solid";

export default {
    components: {
        DownloadIcon,
        XIcon
    },
    props: {
        transaction: {
            type: Object,
            required: true
        }
    },
    emits: ['remove'],
    mounted() {
        console.log(this.transaction);
    }
}
</script>
