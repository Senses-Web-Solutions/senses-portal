<template>
    <ProgressBar colour="danger" :current="current" :max="max" :size="size"></ProgressBar>
</template>
<script>
import ProgressBar from "./ProgressBar";

export default {
    components: {
        ProgressBar
    },
    props: {
        max: {
            type: Number,
            required: true
        },
        current: {
            type: Number,
            default: 0
        },
    },
}
</script>
