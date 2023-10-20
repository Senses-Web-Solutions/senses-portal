<template>
    <Menu as="div" class="relative inline-block text-left">
        <Tooltip>
            <MenuButton class="flex items-center text-zinc-400 bg-zinc-100 rounded-full hover:text-zinc-600 focus:outline-none focus:ring-1 focus:ring-offset-2 focus:ring-offset-zinc-100 focus:ring-indigo-500">
                <GenericIcon :model="{id: user()?.id, colour: user()?.colour, text_colour: user()?.text_colour, initials: user()?.initials}" />
            </MenuButton>
            <template #content>
                My Profile
            </template>
        </Tooltip>

        <transition
            enter-active-class="transition duration-100 ease-out"
            enter-from-class="transform scale-95 opacity-0"
            enter-to-class="transform scale-100 opacity-100"
            leave-active-class="transition duration-75 ease-in"
            leave-from-class="transform scale-100 opacity-100"
            leave-to-class="transform scale-95 opacity-0">
            <MenuItems class="absolute right-0 z-50 w-56 mt-2 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                <div>
                    <MenuItem v-slot="{ active }">
                        <a :href="'/users/' + user()?.id" class="cy_view_user_profile" :class="[active ? 'bg-zinc-100 text-zinc-900' : 'text-zinc-700', 'block px-4 py-2 text-sm']">
                            View Profile
                        </a>
                    </MenuItem>

                    <MenuItem v-if="(user().can('impersonate-user') && (user().can('show-user-setting') || user().can('show-own-user-setting'))) || user().id != actualID" v-slot="{ active }">
                    <a href="#" class="cy_impersonate_user" :class="[active ? 'bg-zinc-100 text-zinc-900' : 'text-zinc-700', 'block px-4 py-2 text-sm']" @click='$asides.push("UserImpersonationForm")'>Impersonate User</a>
                    </MenuItem>

                    <MenuItem v-slot="{ active }">
                    <form action="/logout" method="post" class="mb-0">
                        <button type="submit" :class="[active ? 'bg-zinc-100 text-zinc-900' : 'text-zinc-700']" class="block cy_logout w-full px-4 py-2 text-sm text-left">
                            <input type="hidden" name="_token" :value="csrf" />
                            Logout
                        </button>
                    </form>
                    </MenuItem>
                </div>
            </MenuItems>
        </transition>
    </Menu>
</template>

<script>
import Tooltip from '../../Ui/Tooltip.vue';
import {
    Menu,
    MenuButton,
    MenuItem,
    MenuItems
} from '@headlessui/vue';
import user from '../../../Support/user';
import GenericIcon from '../../Ui/GenericIcon.vue';

export default {
    data() {
        return {
            actualID: window.ciActualCurrentUserID,
            user,
            csrf: null,
        }
    },
    components: {
        GenericIcon,
        Menu,
        MenuButton,
        MenuItem,
        MenuItems,
        Tooltip,
    },
    created() {
        this.csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    }
}
</script>
