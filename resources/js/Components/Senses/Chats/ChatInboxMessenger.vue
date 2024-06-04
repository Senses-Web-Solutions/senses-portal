<template>

</template>

<script>
export default {
    props: {
        chat: {
            type: [Object, null],
            required: true
        }
    },
    data() {
        return {

        }
    },
    watch: {
        chat() {
            console.log(this.chat)
        }
    },
}
</script>