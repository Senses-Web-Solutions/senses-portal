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
import { LoginIcon, MailIcon, LogoutIcon, TrashIcon, CheckIcon, XIcon, EyeIcon, EyeOffIcon, DesktopComputerIcon, StopIcon } from '@heroicons/vue/outline';

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
        CheckIcon,
        XIcon,
        EyeIcon,
        EyeOffIcon,
        DesktopComputerIcon,
        StopIcon
    },

    props: {
        chat: {
            type: Object,
            required: true
        },
        showHistory: {
            type: Boolean,
            default: false
        },
        cobrowsing: {
            type: Boolean,
            default: false
        }
    },

    data() {
        return {
            TransitionTimings,
            user: user()
        }
    },

    computed: {
        yourInvited() {
            return this.chat.invited_agents.find(agent => agent.id === this.user.id);
        },
        yourAssigned() {
            return this.chat.agents.find(agent => agent.id === this.user.id);
        },
        menuItems() {
            const itemArray = [];

            const acceptInvite = {
                title: 'Accept Invite',
                icon: 'CheckIcon',
                action: this.acceptInvite
            };

            const rejectInvite = {
                title: 'Reject Invite',
                icon: 'XIcon',
                action: this.rejectInvite
            };

            const joinChat = {
                title: 'Join',
                icon: 'LoginIcon',
                action: this.joinChat
            };

            const leaveChat = {
                title: 'Leave',
                icon: 'LogoutIcon',
                action: this.leaveChat
            };

            const deleteChat = {
                title: 'Delete',
                icon: 'TrashIcon',
                action: this.deleteChat
            };

            const requestCobrowse = {
                title: 'Request Cobrowse',
                icon: 'DesktopComputerIcon',
                action: this.requestCobrowse
            };

            const stopCobrowse = {
                title: 'Stop Cobrowsing',
                icon: 'StopIcon',
                action: this.stopCobrowse
            };

            const hideHistory = {
                title: 'Hide History',
                icon: 'EyeOffIcon',
                action: this.hideHistory
            };

            const showHistory = {
                title: 'Show History',
                icon: 'EyeIcon',
                action: this.emitShowHistory
            };

            const markAsComplete = {
                title: 'Mark as Complete',
                icon: 'CheckIcon',
                action: () => {
                    // EventHub.emit('chat:close', this.chat.id);
                }
            };

            const inviteAgent = {
                title: 'Invite Agent',
                icon: 'MailIcon',
                action: () => {
                    this.$asides.push('ChatInviteForm', {
                        chat_id: this.chat.id,
                        name: this.chat.name
                    })
                }
            }

            // If you are an invited agent in the list of agents, have an accept and reject button
            if (this.yourInvited) {
                itemArray.push(acceptInvite);
                itemArray.push(rejectInvite);

                return itemArray;
            }

            // If you are not an agent in the list of agents
            if (!this.yourAssigned) {
                itemArray.push(joinChat);
                if (this.showHistory) {
                    itemArray.push(hideHistory);

                } else {
                    itemArray.push(showHistory);
                }
                itemArray.push({ type: 'divider' });
                itemArray.push(deleteChat);

                return itemArray;
            }

            if (this.cobrowsing) {
                itemArray.push(stopCobrowse);
            } else {
                itemArray.push(requestCobrowse);
            }

            if (this.showHistory) {
                itemArray.push(hideHistory);

            } else {
                itemArray.push(showHistory);
            }

            // If you are an agent in the list of agents
            itemArray.push(markAsComplete);
            itemArray.push(inviteAgent);
            itemArray.push(leaveChat);
            itemArray.push({ type: 'divider' });
            itemArray.push(deleteChat);

            return itemArray;
        }
    },

    methods: {
        fetchChats() {
            EventHub.emit('chats:fetch');
        },

        acceptInvite() {
            axios.get(`/api/v2/accept/invite/chats/${this.chat.id}`);
        },

        rejectInvite() {
            axios.get(`/api/v2/reject/invite/chats/${this.chat.id}`);
        },

        joinChat() {
            axios.get(`/api/v2/join/chats/${this.chat.id}`);
        },

        requestCobrowse() {
            axios.get(`/api/v2/cobrowse/chats/${this.chat.id}`);
        },

        stopCobrowse() {
            axios.get(`/api/v2/stop/cobrowse/chats/${this.chat.id}`)
                .then(response => {
                    EventHub.emit('chats:cobrowse:stop', response.data);
                });
        },

        inviteAgent() {
            this.$asides.push('ChatInviteForm', {
                chat_id: this.chat.id
            })
        },

        leaveChat() {
            axios.get(`/api/v2/leave/chats/${this.chat.id}`);
        },

        deleteChat() {
            axios.delete(`/api/v2/chats/${this.chat.id}`)
                .then(response => {
                    EventHub.emit('chats:delete', this.chat.id);
                });
        },

        emitShowHistory() {
            EventHub.emit('chats:show-history');
        },

        hideHistory() {
            EventHub.emit('chats:hide-history');
        },
    }
}
</script>