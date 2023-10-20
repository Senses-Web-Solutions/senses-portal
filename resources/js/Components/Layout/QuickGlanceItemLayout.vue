<template>
    <div v-if="slotEmpty($slots.default)" :class="classes">
        <slot></slot>
    </div>
</template>

<script>
import slotEmpty from "../../Support/slotEmpty";

export default {

    methods: {
        slotEmpty
    },

    props: {
        rightSize: {
            type: String,
            default: 'md'
        },
        columnNo: {
            type: String,
            default: '2'
        }
    },
    computed: {
        classes(){
            var classes = 'grid gap-2';
            // if(this.rightSize == 'lg'){
            //     classes += ' !grid-cols-3 [&>*:nth-child(even)]:col-span-2';
            // }
            classes += ' grid-cols-' + this.columnNo;
            return classes;
        }
    }
}
</script>
