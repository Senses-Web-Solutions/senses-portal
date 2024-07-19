<template>
    <div class="flex items-center ml-4 md:ml-6 space-x-3">
        <NotificationTray v-if="navbarIcons && navbarIcons.notifications && user().layout !== 'customer' && (user().can('list-own-notification') || user().can('list-notification'))" />

        <Switch v-model="darkMode" :class="[darkMode ? 'bg-zinc-400 ring-zinc-300' : 'bg-zinc-200 ring-yellow-500', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2']">
            <span class="sr-only">Use setting</span>
            <span :class="[darkMode ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none relative inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']">
                <span :class="[darkMode ? 'opacity-0 duration-100 ease-out' : 'opacity-100 duration-200 ease-in', 'absolute inset-0 flex h-full w-full items-center justify-center transition-opacity']" aria-hidden="true">
                    <SunIcon class="h-3 w-3 text-yellow-500"></SunIcon>
                </span>
                <span :class="[darkMode ? 'opacity-100 duration-200 ease-in' : 'opacity-0 duration-100 ease-out', 'absolute inset-0 flex h-full w-full items-center justify-center transition-opacity']" aria-hidden="true">
                    <MoonIcon class="h-3 w-3 text-zinc-900"></MoonIcon>
                </span>
            </span>
        </Switch>

        <!-- Profile dropdown -->
        <UserIconButton class="relative !ml-5" id="user-icon-button" />
    </div>
</template>

<script>

import NotificationTray from '../Senses/NotificationTray.vue';

import {
    ChatAlt2Icon,
    MailIcon,
    GlobeIcon,
    PlusCircleIcon,
} from '@heroicons/vue/outline';

import {
    SunIcon,
    MoonIcon,
} from '@heroicons/vue/solid';

import UserIconButton from '../Senses/Users/UserIconButton.vue';
import { loadClientConfig } from '../../Support/client';
import { getBackendClientConfig } from '../../Support/client';
import { capitalize } from 'lodash-es';

import { Switch } from '@headlessui/vue'

export default {
    components: {
        NotificationTray,
        ChatAlt2Icon,
        MailIcon,
        GlobeIcon,
        PlusCircleIcon,
        UserIconButton,
        Switch,
        SunIcon,
        MoonIcon,
    },

    props: {
        //
    },

    data() {
        return {
            user: window.user,
            config: getBackendClientConfig(),
            navbarIcons: {
                messenger: false,
                notifications: true,
                clientApplications: false,
                inbox: false,
                addHoliday: false,
                addTask: true,
            },

            darkMode: false,
        }
    },

    mounted() {
        let vm = this;
        loadClientConfig(window.SENSES_CLIENT, function (clientData) {
            if(clientData.navbarIcons) {
                vm.navbarIcons = clientData.navbarIcons;
            }
        });

        if (!window.getCookie('theme') && window.matchMedia("(prefers-color-scheme: dark)")) {
            window.setCookie('theme', 'dark');
        }

        this.darkMode = localStorage.getItem('darkMode') === "true";
        window.localStorage.setItem('darkMode', this.darkMode ? "true" : "false")
    },

    methods: {
        capitalize,
    },

    watch: {
        darkMode(v) {
            if (v) {
                document.getElementById('app').classList.add('dark');
                window.setCookie('theme', 'dark');
                window.localStorage.setItem('darkMode', "true")
            } else {
                document.getElementById('app').classList.remove('dark');
                window.setCookie('theme', 'light');
                window.localStorage.setItem('darkMode', "false")
            }
        }
    },
}
</script>
