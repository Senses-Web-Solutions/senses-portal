<template>
    <SecondaryButton @click="form ? $asides.push(form, data) : $emit('click', $event)" :id="kebabCase(form + '_add_button')">
        <slot>
            <PlusIcon class="mx-auto" :class="size"/>
        </slot>
    </SecondaryButton>
</template>
<script>
import { PlusIcon } from "@heroicons/vue/outline";
import SecondaryButton from './SecondaryButton.vue';
import kebabCase from "../../../Filters/KebabCase";

export default {
    components: { SecondaryButton, PlusIcon },
    props: {
        form: {
            type: String,
            default:null
        },
        data: {
            type: Object,
            default: () => ({})
        },
        tableButton:{
            type:Boolean,
            default:false
        }
    },
    computed: {
        size() {
            return this.tableButton ? "w-5 h-5" : "w-4 h-4";
        }
    },
    emits:['click'],
    methods: {
        kebabCase,
    }
}
</script>
