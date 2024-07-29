<template>
    <div class="flex h-screen overflow-hidden bg-zinc-100" :class="{ 'bg-zinc-100': !flushLayout }">
        <div class="flex flex-col flex-1 w-0 overflow-hidden">
            <main class="relative z-0 flex-1 overflow-y-auto focus:outline-none" tabindex="0">
                <slot name="content" />
            </main>
        </div>
    </div>
</template>

<script>

import {
    HomeIcon,
    GlobeIcon,
    XIcon,
    PlusCircleIcon,
    MenuAlt2Icon,
    SearchIcon,
    ChatAlt2Icon,
    MailIcon
} from '@heroicons/vue/outline';

import { ref } from 'vue';
import { useMediaQuery } from '@vueuse/core';
import FadeTransition from '../Ui/Transitions/FadeTransition.vue';
import Sidebar from '../Senses/Sidebar/Sidebar.vue';
import CondensedSidebar from '../Senses/Sidebar/CondensedSidebar.vue';
import Tooltip from '../Ui/Tooltip.vue';
import NavbarButtons from './NavbarButtons.vue';
import useTailwindConfig from '../../Support/useTailwindConfig';

// vue will auto unwrap this when not used in
// the composition api. Use it like a regular data var.
const flushLayout = ref(false);

import useEcho from '../../Support/useEcho';

const echo = useEcho();

export default {
    components: {
        FadeTransition,
        Tooltip,
        ChatAlt2Icon,
        NavbarButtons,
        HomeIcon,
        GlobeIcon,
        PlusCircleIcon,
        XIcon,
        MenuAlt2Icon,
        MailIcon,
        Sidebar,
        CondensedSidebar,
        SearchIcon
    },

    provide() {
        return {
            flushLayout,
        };
    },

    data() {
        return {
            flushLayout,
            showDatabases: false,
            showProducts: false,
            sidebarOpen: true,
            showIntel: false,
            user: window.user,
            isDesktop: true || useMediaQuery(`(min-width: ${useTailwindConfig()?.theme?.screens?.md})`)
        };
    },

    watch: {
        isDesktop(newVal, oldVal) {
            if (oldVal && !newVal) {
                // from desktop -> mobile
                this.sidebarOpen = false;
            }

            if (newVal && !oldVal) {
                // from mobile -> desktop
                this.sidebarOpen = true;
            }
        }
    },

    mounted() {
        this.sidebarOpen = !!this.isDesktop;

        echo.private(`servers.11.deploy`).listen('Servers\\ServerDeployed', ({data}) => {
            console.log(data);

            if (data.status == 'completed') {
                window.location.reload();
            }
        })
    },

    methods: {
        search() {
            this.$modals.push('GlobalSearch', { hideCloseButton: true, flush: true });
        },
    },
};
</script>
