<template>
    <span class="font-medium text-zinc-500">
        <CheckIcon class="h-4 w-4" v-if="data"/>
        <XIcon class="h-4 w-4" v-else/>
    </span>
</template>
<script>
import {CheckIcon, XIcon} from "@heroicons/vue/outline";
export default {
    components: { XIcon, CheckIcon },
    props: {
        data: {
            default: false
        }
    }
}
</script>
