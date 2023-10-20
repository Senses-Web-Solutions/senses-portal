<template>
    <div>
        <div class="border-b border-zinc-200 w-full">
            <nav class="w-full flex space-x-8" aria-label="Tabs" :class="{ 'justify-between': fullWidth, 'justify-center': !fullWidth }">
                <slot></slot>
            </nav>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        fullWidth: {
            type: Boolean,
            default: false
        }
    }
}
</script>
