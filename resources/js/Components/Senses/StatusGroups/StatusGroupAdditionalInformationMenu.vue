<template>
    <SeMenu :data="data">
        <template #items>
            <MenuItem v-for="(menuItem, index) in menuItems" :disabled="!user().can(menuItem.permission)" :style="{
                '--tw-transition-delay': `${index * TransitionTimings.STAGGERED_LIST_DELAY
                    }ms`,
            }" :key="`additionalInformation${index}`" @click="menuItem.action">
            <template #icon>
                <component :is="menuItem.icon" />
            </template>
            {{ menuItem.title }}
            </MenuItem>
        </template>
    </SeMenu>
</template>
<script>
import {
    CurrencyPoundIcon,
    CheckCircleIcon,
    MinusCircleIcon,
    OfficeBuildingIcon,
    TruckIcon,
} from '@heroicons/vue/solid';
import MenuItem from '../../Ui/Menu/MenuItem.vue';
import TransitionTimings from '../../../Enums/TransitionTimings';
import SeMenu from '../../Ui/Menu/SeMenu.vue';

export default {
    components: {
        CurrencyPoundIcon,
        CheckCircleIcon,
        MenuItem,
        OfficeBuildingIcon,
        TruckIcon,
        MinusCircleIcon,
        SeMenu,
    },
    props: {
        data: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            user: window.user,
            TransitionTimings,
        };
    },
    computed: {
        menuItems() {
            return [
            ];
        },
    },
};
</script>
