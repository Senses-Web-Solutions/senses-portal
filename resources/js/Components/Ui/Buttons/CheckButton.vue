<template>
    <button
        class="inline-flex items-center text-green-400 p-1 border rounded-full shadow-sm hover:shadow-md bg-white focus:outline-none">
        <CheckIcon class="h-4 w-4"/>
    </button>
</template>
<script>
import {CheckIcon} from '@heroicons/vue/outline';
export default {
    components: {CheckIcon},
    props: {},
    computed: {},
};
</script>
