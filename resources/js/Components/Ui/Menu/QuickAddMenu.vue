<template>
    <SeMenu
        :button="false"
        class="text-zinc-400 hover:text-zinc-500"
    >
        <template #icon>
            <PlusCircleIcon class="h-5 w-5" />
        </template>
        <template #items>
            <MenuItem
                :key="`quickAddMenu${0}`"
                :style="{
                    '--tw-transition-delay': `${0}ms`,
                }"
                :disabled="!user().can('create-task')"
                @click="$asides.push('TaskForm')"
            >
                <template #icon>
                    <CollectionIcon />
                </template>
                <template #default> Add Task </template>
            </MenuItem>
            <MenuItem
                :key="`quickAddMenu${1}`"
                :style="{
                    '--tw-transition-delay': `${1}ms`,
                }"
                :disabled="!user().can('create-holiday')"
                @click="$asides.push('HolidayForm', { userId: user().id })"
            >
                <template #icon>
                    <GlobeIcon />
                </template>
                <template #default> Add Holiday </template>
            </MenuItem>
        </template>
    </SeMenu>
</template>
<script>
import { CollectionIcon, GlobeIcon } from '@heroicons/vue/solid';
import { PlusCircleIcon } from '@heroicons/vue/outline';
import TransitionTimings from '../../../Enums/TransitionTimings';
import SeMenu from '../../Ui/Menu/SeMenu.vue';
import MenuItem from '../../Ui/Menu/MenuItem.vue';

export default {
    components: {
        MenuItem,
        SeMenu,
        CollectionIcon,
        PlusCircleIcon,
        GlobeIcon,
    },
    data() {
        return {
            user: window.user,
            TransitionTimings,
        };
    },
};
</script>
