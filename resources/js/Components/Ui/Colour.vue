<template>
    <div class="h-3 w-3 rounded-full my-auto" :class="'bg-' + colour" aria-hidden="true"></div>
</template>

<script>

export default {
    props: {
        // hex: {
        //     type: String
        // },
        colour: {
            type: String,
            default: 'zinc-500'
        }
    }
}

</script>
