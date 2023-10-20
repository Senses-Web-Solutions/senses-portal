<template>
    <MenuItem v-slot="{ active }">
        <button
            class="text-left transition"
            :class="[active ? 'bg-zinc-100 ' : '', 'group flex w-full items-center py-2 px-3 text-zinc-700']"
        >
            <div v-if="$slots.icon" class="my-auto mr-3 h-5 w-5 text-zinc-400 transition group-hover:text-zinc-500">
                <slot name="icon"></slot>
            </div>
            <span v-if="dot" class="mr-2 h-1.5 w-1.5 rounded-full" :class="!colourIsHex ? dotColour : null" :style="{ 'background-color': colourIsHex ? dotColour : null }" aria-hidden="true"></span>
            <slot :active="active" />
        </button>
    </MenuItem>
</template>

<script>
import { MenuItem } from '@headlessui/vue';

export default {
    props: {
        dotColour: {
            type: String,
            default: null,
        },
        dot: {
            type: Boolean,
        },
    },

    components: {
        MenuItem,
    },

    setup(props) {
        const colourIsHex = props.dotColour ? /^#[0-9A-F]{6}$/i.test(props.dotColour) : null;

        return {
            colourIsHex
        };
    },
};
</script>
