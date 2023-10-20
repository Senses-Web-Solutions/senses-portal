<template>
    <div class="p-5 sm:grid sm:grid-cols-3 sm:gap-4 gap-2">
        <StrongText class="sm:col-span-1 capitalize"><slot name="title"></slot></StrongText>
        <div class="sm:col-span-2">
            <slot></slot>
        </div>
    </div>
</template>
<script>
import StrongText from '../Text/StrongText.vue';
export default {
    components: { StrongText }
}
</script>
