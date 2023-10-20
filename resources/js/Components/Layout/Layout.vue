<template>
    <div class="flex h-screen overflow-hidden bg-zinc-100" :class="{ 'bg-zinc-100': !flushLayout }">
        <div class="fixed inset-0 z-40 hidden" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-zinc-600 bg-opacity-75" aria-hidden="true"></div>
            <div class="relative flex flex-col flex-1 w-full max-w-xs pt-5 pb-4 bg-zinc-800">
                <div class="absolute top-0 right-0 pt-2 -mr-12">
                    <button class="flex items-center justify-center w-10 h-10 ml-1 rounded-full focus:outline-none focus:ring-1 focus:ring-inset focus:ring-white">
                        <span class="sr-only">Close sidebar</span>
                        <XIcon class="w-5 h-5 text-white" />
                    </button>
                </div>

                <div class="flex items-center px-4 shrink-0">
                    <img class="w-auto h-8" src="/images/logo.svg" alt="Community Information">
                </div>

                <div class="flex-1 h-0 mt-5 overflow-y-auto">
                    <nav class="px-2 space-y-1">
                        <a href="#" class="flex items-center px-2 py-2 text-lg font-normal text-white bg-zinc-900 rounded-md group">
                            <HomeIcon />
                            Dashboard
                        </a>
                    </nav>
                </div>
            </div>

            <div class="shrink-0 w-14" aria-hidden="true"></div>
        </div>

        <!-- TODO Clean this up -->
        <Transition enter-active-class="transition duration-300 ease-out" enter-from-class="-translate-x-full" enter-to-class="translate-x-0" leave-active-class="transition duration-300 ease-in"
            leave-from-class="translate-x-0 opacity-100" leave-to-class="-translate-x-full opacity-0">
            <div v-if="sidebarOpen" class="fixed top-0 bottom-0 left-0 z-50 bg-opacity-100 md:relative md:flex md:shrink-0 md:z-0 backdrop-blur-lg">
                <div class="flex flex-col w-56 h-full shadow-md">
                    <div class="flex flex-col flex-1 h-0">
                        <a href="/" class="flex items-center px-4 bg-white border-b border-zinc-200 h-14 shrink-0">
                            <img class="w-auto h-8" src="/images/logo.svg" alt="Community Information">
                            <div class='pl-2 pt-1 text-xl font-medium text-black'>
                                Senses
                            </div>
                        </a>

                        <div class="flex flex-col flex-1 overflow-y-auto bg-white border-r border-gray-200">
                            <Sidebar />
                        </div>
                    </div>
                </div>
            </div>
        </Transition>

        <FadeTransition>
            <div v-if="sidebarOpen" class="fixed inset-0 z-40 bg-black bg-opacity-25 md:hidden" @click="sidebarOpen = false"></div>
        </FadeTransition>

        <div class="flex flex-col flex-1 w-0 overflow-hidden">
            <div class="relative flex bg-white border-b border-zinc-200 h-14 shrink-0 z-8">
                <button class="px-4 text-zinc-500 border-r border-zinc-200 focus:outline-none focus:ring-1 focus:ring-inset focus:ring-primary-500 md:hidden" @click="sidebarOpen = !sidebarOpen">
                    <span class="sr-only">Open sidebar</span>
                    <MenuAlt2Icon class="w-5 h-5" />
                </button>

                <div class="flex justify-between flex-1 px-4" v-if="user()">
                    <div class="flex flex-1">
                        <form class="flex w-full mb-0 md:ml-0" action="#" method="GET">
                            <label for="search_field" class="sr-only">Search</label>
                            <div class="relative w-full text-zinc-400 focus-within:text-zinc-600">
                                <div class="absolute inset-y-0 left-0 flex items-center pointer-events-none">
                                    <SearchIcon class="w-5 h-5" />
                                </div>
                                <input
                                    id="search_field"
                                    class="block w-full h-full py-2 pl-8 pr-3 placeholder-zinc-500 bg-white border-transparent focus:outline-none focus:placeholder-zinc-400 focus:ring-0 focus:border-transparent"
                                    placeholder="Search" type="search" name="search" @focus="search">
                            </div>
                        </form>
                    </div>

                    <!-- Top Right Buttons -->
                    <NavbarButtons/>
                </div>
            </div>

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
    },

    methods: {
        search() {
            this.$modals.push('GlobalSearch', { hideCloseButton: true, flush: true });
        },
    },
};
</script>
