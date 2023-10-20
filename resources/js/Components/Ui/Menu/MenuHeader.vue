<template>
    <div class="px-3 pt-2 pb-2 text-sm font-medium tracking-widest text-zinc-600 uppercase border-b" :class="{align: align}">
        <slot/>
    </div>
</template>

<script>

export default {
    props: {
        align: {
            type: String,
            default: "text-left"
        }
    },
    components: {
    },
};
</script>
