<template>
    <MenuItems
        ref="element"
        as="div"
        :class="`z-10 mt-2 w-56 origin-top rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 transition focus:outline-none`"
    >
        <slot />
    </MenuItems>
</template>
<script setup>
import { MenuItems } from '@headlessui/vue';
import { ref } from 'vue';
import useHeightReflow from '../../../Support/useHeightReflow';

const element = ref(null);
useHeightReflow(element);
</script>
