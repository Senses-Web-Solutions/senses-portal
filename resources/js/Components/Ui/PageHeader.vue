<template>
    <section class="sticky top-0 z-50 flex items-center justify-between w-full px-8 py-4 bg-white shadow">
        <div class="my-1">
            <h3 id="page-header-title" class="pb-1 text-2xl font-medium leading-6 text-zinc-900">
                <slot name="title"></slot>
            </h3>
            <p class="pt-2 text-sm font-medium leading-4 text-zinc-500" v-if="$slots.subtitle">
                <slot name="subtitle"></slot>
            </p>
        </div>
        <ButtonGroup>
            <slot name="actions"></slot>
        </ButtonGroup>
    </section>
</template>
<script>
import ButtonGroup from './Buttons/ButtonGroup.vue';

export default {
    components: {ButtonGroup},
}
</script>
