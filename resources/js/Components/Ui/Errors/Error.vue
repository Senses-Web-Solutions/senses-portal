<template>
    <p class="text-red-400 text-sm mt-1"> {{message}}</p>
</template>
<script>
    export default {
        props: {
            message: {
                required: true
            },
        },

        methods: {}
    }
</script>
