<template>
    <div class="p-2 hover:bg-zinc-200 active:bg-zinc-300 transition cursor-pointer flex items-center gap-4">
        <FolderIcon class="w-8 h-8 text-zinc-500" />
        <div class="capitalize"><slot /></div>
        <div class="ml-auto" v-if="$slots.end"><slot name="end" /></div>
    </div>
</template>
<script>
import { FolderIcon } from "@heroicons/vue/solid";
export default {
    props: {
        folder:{
            default: null,
        }
    },
    components: { FolderIcon },
};
</script>
