<template>
    <SeMenu>
        <template #items>
            <MenuItem
                v-for="(menuItem, index) in menuItems"
                :disabled="!user().can(menuItem.permission)"
                :style="{
                    '--tw-transition-delay': `${
                        index * TransitionTimings.STAGGERED_LIST_DELAY
                    }ms`,
                }"
                :key="`additionalInformation${index}`"
                @click="menuItem.action"
            >
                <template #icon>
                    <component :is="menuItem.icon" />
                </template>
                {{ menuItem.title }}
            </MenuItem>
        </template>
    </SeMenu>
</template>
<script>
import MenuItem from '../../Ui/Menu/MenuItem.vue';
import { UserRemoveIcon } from '@heroicons/vue/outline';
import TransitionTimings from '../../../Enums/TransitionTimings';
import SeMenu from '../../Ui/Menu/SeMenu.vue';

export default {
    components: {
        UserRemoveIcon,
        MenuItem,
        TransitionTimings,
        SeMenu,
    },
    props: {
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
                    {
                        title: 'View Hidden Users',
                        permission: 'list-hidden-user',
                        action: () => window.open('/hidden-users'),
                        icon: 'UserRemoveIcon',
                    },
                ];
        },
    },
};
</script>
