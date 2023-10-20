<template>
    <SeMenu :data="data">
        <template #items>
            <MenuItem
                v-for="(menuItem, index) in menuItems"
                :key="`tableSettings${index}`"
                :disabled="!user().can(menuItem.permission)"
                :style="{
                    '--tw-transition-delay': `${
                        index * TransitionTimings.STAGGERED_LIST_DELAY
                    }ms`,
                }"
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
import TransitionTimings from '../../../Enums/TransitionTimings';
import SeMenu from '../../Ui/Menu/SeMenu.vue';

export default {
    components: {
        MenuItem,
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
                {
                    title: 'Clear Filters',
                    permission: 'create-asset-maintenance',
                    action: () =>
                        this.$asides.push('AssetMaintenanceForm', {
                            assetId: this.data.id,
                        }),
                    icon: 'TruckIcon',
                },
            ];
        },
    },
};
</script>
