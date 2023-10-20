<template>
    <span class="font-medium text-zinc-500 flex justify-center">
        <div v-if="data" class=" rounded-2xl bg-green-500/50 p-2 border-2 border-green-500">
            <CheckIcon class="h-5 w-6 text-white" />
        </div>
        <div v-else class=" rounded-2xl bg-red-500/50 p-2 border-2 border-red-500">
            <XIcon class="h-5 w-6 text-white" />
        </div>
    </span>
</template>
<script>
import { CheckIcon, XIcon } from "@heroicons/vue/outline";
export default {
    components: { XIcon, CheckIcon },
    props: {
        data: {
            default: false
        }
    }
}
</script>
