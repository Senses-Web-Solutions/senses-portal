<template>
    <button
        class="inline-flex items-center text-red-400 p-1 border rounded-full shadow-sm hover:shadow-md bg-white focus:outline-none">
        <XIcon class="h-4 w-4"/>
    </button>
</template>
<script>
import {XIcon} from '@heroicons/vue/outline';
export default {
    components: {XIcon},
    props: {},
    computed: {},
};
</script>
