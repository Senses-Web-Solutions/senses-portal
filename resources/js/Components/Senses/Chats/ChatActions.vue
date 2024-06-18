<template>
    <SeMenu :data="chat">
        <template #items>
            <template v-for="(menuItem, index) in menuItems" :key="`taskButtonsMenu${index}`">

                <MenuDivider v-if="menuItem.type && menuItem.type == 'divider'" />
                <MenuItem v-else :disabled="(menuItem.disabled && menuItem.disabled()) || menuItem.permssion && !user().can(menuItem.permission)" :style="{
                    '--tw-transition-delay': `${index * TransitionTimings.STAGGERED_LIST_DELAY
                        }ms`,
                }" :key="`additionalInformation${index}`" @click="menuItem.action">
                <template #icon>
                    <component :is="menuItem.icon" />
                </template>
                {{ menuItem.title }}
                </MenuItem>
            </template>
        </template>
    </SeMenu>
</template>
<script>
import axios from 'axios';
import { LoginIcon, MailIcon, LogoutIcon, TrashIcon, CheckIcon } from '@heroicons/vue/outline';

import SeMenu from '../../Ui/Menu/SeMenu.vue';
import MenuItem from '../../Ui/Menu/MenuItem.vue';
import MenuDivider from '../../Ui/Menu/MenuDivider.vue';
import SecondaryButton from '../../Ui/Buttons/SecondaryButton.vue';

import TransitionTimings from '../../../Enums/TransitionTimings';
import EventHub from '../../../Support/EventHub';
import user from '../../../Support/user';

export default {
    components: {
        SeMenu,
        MenuItem,
        MenuDivider,
        SecondaryButton,

        LoginIcon,
        MailIcon,
        LogoutIcon,
        TrashIcon,
        CheckIcon
    },

    props: {
        chat: {
            type: Object,
            required: true
        }
    },

    data() {
        return {
            TransitionTimings,
            user: user()
        }
    },

    computed: {
        menuItems() {
            const itemArray = [];

            // If you are not an agent in the list of agents
            if (!this.chat.agents.find(agent => agent.id === this.user.id)) {
                itemArray.push({
                    title: 'Join',
                    icon: 'LoginIcon',
                    action: this.joinChat
                });

                itemArray.push({
                    title: 'Delete',
                    icon: 'TrashIcon',
                    action: this.deleteChat
                });

                return itemArray;
            }

            // If you are an agent in the list of agents
            itemArray.push({
                title: 'Invite Agent',
                icon: 'MailIcon',
                action: () => {
                    this.$asides.push('ChatInviteForm', {
                        chat_id: this.chat.id,
                        name: this.chat.name
                    })
                }
            });

            itemArray.push({
                title: 'Leave',
                icon: 'LogoutIcon',
                action: this.leaveChat
            });

            itemArray.push({
                title: 'Mark as complete',
                icon: 'CheckIcon',
                action: () => {
                    // EventHub.emit('chat:close', this.chat.id);
                }
            });

            itemArray.push({
                title: 'Delete',
                icon: 'TrashIcon',
                action: this.deleteChat
            });

            return itemArray;
        }
    },

    methods: {
        fetchChats() {
            EventHub.emit('chats:fetch');
        },

        joinChat() {
            axios.get(`/api/v2/join/chats/${this.chat.id}`)
                .then(response => {
                    EventHub.emit('chats:join', response.data);
                });
        },

        inviteAgent() {
            console.log('Invite Agent');
            this.$asides.push('ChatInviteForm', {
                chat_id: this.chat.id
            })
        },

        leaveChat() {
            axios.get(`/api/v2/leave/chats/${this.chat.id}`)
                .then(response => {
                    EventHub.emit('chats:leave', response.data);
                });
        },

        deleteChat() {
            axios.delete(`/api/v2/chats/${this.chat.id}`)
                .then(response => {
                    EventHub.emit('chats:delete', this.chat.id);
                });
        },

        inviteAgent() {}
    }
}
</script>