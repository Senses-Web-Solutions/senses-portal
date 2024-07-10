<template>
    <div ref="element" class="w-64 space-y-2 rounded-lg" :class="{'bg-white': !isDark, 'bg-black': isDark}">
        <template v-if="user">
            <div class="divide-y" :class="{'divide-zinc-200': !isDark, 'divide-zinc-700': isDark}">
                <div class="p-3 flex items-center text-center justify-center" :class="{'text-black': !isDark, 'text-white': isDark}">
                    <div class="space-y-2">
                        <UserIcon :user="user" height="10" width="10" text-size="lg"></UserIcon>
                        <div>
                            <Text class="text-md" :class="{'text-black': !isDark, 'text-white': isDark}">{{ user.first_name }} {{ user.last_name }}</Text>
                            <Text class="text-sm" :class="{'text-black': !isDark, 'text-white': isDark}">{{ user.job_title }}</Text>
                        </div>
                        <div class="text-zinc-400 text-sm space-y-1">
                            <div class="flex items-center justify-center mr-2" v-if="user.mobile">
                                <PhoneIcon class="h-3 w-3 mr-1"></PhoneIcon>
                                <Text :class="{'text-black': !isDark, 'text-white': isDark}">{{ user.mobile }}</Text>
                            </div>
                            <div class="flex items-center justify-center mr-2" v-if="user.email">
                                <MailIcon class="h-3 w-3 mr-1"></MailIcon>
                                <Text :class="{'text-black': !isDark, 'text-white': isDark}">{{ user.email }}</Text>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-3">
                    <div class="flex justify-center space-x-4 items-center">
                        <SecondaryButton size="xs" @click="goToUser">View Profile</SecondaryButton>
                    </div>
                </div>
            </div>
        </template>
        <div v-else class="flex items-center justify-center p-3">
            <LoadingIcon class="my-5 h-8 w-8 text-primary" />
        </div>
    </div>
</template>
<script>
import axios from 'axios';
import SecondaryButton from '../../Ui/Buttons/SecondaryButton.vue';
import LoadingIcon from '../../Ui/LoadingIcon.vue';
import Text from '../../Ui/Text/Text.vue';
import UserIcon from './UserIcon.vue';
import {
    PhoneIcon,
    CollectionIcon,
    MailIcon,
    LocationMarkerIcon
} from '@heroicons/vue/outline';
export default {
    components: { SecondaryButton, CollectionIcon, LocationMarkerIcon, MailIcon, UserIcon, LoadingIcon, Text, PhoneIcon },
    props: {
        id: {
            type: [Number, String],
            required: true,
        },
    },
    data() {
        return {
            currentUser: window.user,
            user: null,
            isDark: window.localStorage.getItem('darkMode') === 'true',
        }
    },
    mounted() {
        axios.get(`/api/v2/users/${this.id}/popover-content`).then((response) => {
            this.user = response.data;
        });
    },
    methods: {
        goToUser() {
            if (
                this.currentUser().can('show-user') ||
                (this.currentUser().can('show-own-user') &&
                    this.user.id === this.user().id) ||
                (this.currentUser().can('show-managed-user') &&
                    this.currentUser().managed_user_ids.includes(this.user.id))
            ) {
                window.open(`/users/${this.user.id}`, "_blank");
            }
        },
    },
};
</script>
