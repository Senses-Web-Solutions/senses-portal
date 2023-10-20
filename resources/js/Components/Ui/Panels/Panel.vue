<template>
    <section class="px-5 mb-4 bg-white">
        <div v-if="$slots.title" class="flex justify-between pt-5" :class="{ 'border-b' : border, 'pb-5' : !!border}">
            <SectionTitle>
                <slot name="title"></slot>
            </SectionTitle>
            <ButtonGroup>
                <slot name="actions"></slot>
            </ButtonGroup>
        </div>
        <div class="py-5">
            <slot></slot>
        </div>
    </section>
</template>
<script>
import SectionTitle from '../Sections/SectionTitle.vue';
import ButtonGroup from '../Buttons/ButtonGroup.vue';

export default {
    components: {
        SectionTitle,
        ButtonGroup
    },
    props: {
        border: {
            type: Boolean,
            default: true
        }
    }
};
</script>
