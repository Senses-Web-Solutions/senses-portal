<template>
        
    <div>
        <p class="text-black">{{ currentFileIndex + 1 }} / {{ filesLength }}</p>
    </div>
    <div class="flex">
        <Tooltip>
            <SecondaryButton
                class="translate-x-[2px]"
                rounded="left"
                size="sm"
                @click="previousFile"
            >
                <ArrowLeftIcon class="w-5 h-5" />
            </SecondaryButton>
            <template #content>
                Previous
            </template>
        </Tooltip>
        <Tooltip>
            <SecondaryButton
                class="translate-x-[1px]"
                rounded="right"
                size="sm"
                @click="nextFile"
            >
                <ArrowRightIcon class="w-5 h-5" />
            </SecondaryButton>
            <template #content>
                Next
            </template>
        </Tooltip>
    </div>
    <SecondaryButton @click="toggleMeta">{{viewMeta ? 'Hide' : 'Show'}} Meta</SecondaryButton>
</template>
<script>
import { ArrowLeftIcon, ArrowRightIcon } from '@heroicons/vue/outline';
import SecondaryButton from '../../Ui/Buttons/SecondaryButton.vue';
import Tooltip from '../../Ui/Tooltip.vue';

export default {
components: {
ArrowLeftIcon,
ArrowRightIcon,
SecondaryButton,
Tooltip,
},
props: {
filesLength: {
    type: Number,
    default: 0,
},
currentFileIndex: {
    type: Number,
    default: 0,
},
viewMeta: {
    type: Boolean,
    default: false,
},
},
emits: ['update:currentFileIndex', 'update:viewMeta'],
mounted() {
window.addEventListener('keydown', this.handleKeyDown);
},
beforeUnmount() {
window.removeEventListener('keydown', this.handleKeyDown);
},
methods: {
nextFile() {
    this.$emit('update:currentFileIndex', (this.currentFileIndex + 1) % this.filesLength);
},
previousFile() {
    this.$emit('update:currentFileIndex', (this.currentFileIndex - 1 + this.filesLength) % this.filesLength);
},
toggleMeta() {
    this.$emit('update:viewMeta', !this.viewMeta);
},
handleKeyDown(e) {
    const {key} = e;
    
    if (key === 'ArrowRight') {
        this.nextFile();
    } else if (key === 'ArrowLeft') {
        this.previousFile();
    }
},
},
};
</script>