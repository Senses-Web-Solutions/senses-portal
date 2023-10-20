<template>
    <div class="rounded-md transition cursor-pointer text-center text-sm">
        <FolderIcon class="w-20 h-20 text-zinc-500" />
        <div class="capitalize pt-2 w-20"><slot /></div>
    </div>
</template>
<script>
import { FolderIcon } from '@heroicons/vue/solid';
export default {
    props: {
        folder:{
            default:null,
        },
        condensed: Boolean
    },
    components: { FolderIcon }
}
</script>
