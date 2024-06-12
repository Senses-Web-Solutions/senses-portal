<template>
    <div 
        class="flex justify-between" 
        :class="{
            'flex-row': horizontal,
            'flex-col space-y-2': !horizontal,
        }"
    >
        <SeLabel class="capitalize"><slot name="title"></slot></SeLabel>
        <div>
            <slot></slot>
        </div>
    </div>
</template>
<script>
import SeLabel from '../../Ui/Inputs/SeLabel.vue';

export default {
    components: { SeLabel },
    props: {
        horizontal: {
            type: Boolean,
            default: true,
        },
    }
}
</script>
