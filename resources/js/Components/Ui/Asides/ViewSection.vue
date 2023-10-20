<template>
    <div class="py-4 px-6 space-y-2">
        <p class="truncate text-lg text-primary-700 transition text-zinc-700 font-medium">
            <slot name="title"></slot>
        </p>
        <div class="grid gap-4" :class="condensed ? 'grid-cols-1' : 'grid-cols-2'">
            <slot></slot>
        </div>
    </div>
</template>
<script>

export default {
    props: {
        condensed: {
            type: Boolean,
            default: false
        }
    }
}

</script>
