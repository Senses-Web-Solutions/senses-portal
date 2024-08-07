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
import { LoginIcon, MailIcon, LogoutIcon, TrashIcon, CheckIcon, XIcon, EyeIcon, EyeOffIcon, DesktopComputerIcon, StopIcon, UserIcon, InformationCircleIcon, BookOpenIcon } from '@heroicons/vue/outline';

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
        StopIcon,
        UserIcon,
        InformationCircleIcon,
        BookOpenIcon
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
        showDetails: {
            type: Boolean,
            default: false
        },
        cobrowsing: {
            type: Boolean,
            default: false
        },
        historical: {
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
            let itemArray = [];

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
                icon: 'BookOpenIcon',
                action: this.emitHideHistory
            };

            const showHistory = {
                title: 'View History',
                icon: 'BookOpenIcon',
                action: this.emitShowHistory
            };

            const showDetails = {
                title: 'View Details',
                icon: 'InformationCircleIcon',
                action: this.emitShowDetails
            };

            const hideDetails = {
                title: 'Hide Details',
                icon: 'InformationCircleIcon',
                action: this.emiteHideDetails
            };

            const markAsComplete = {
                title: 'Mark as Complete',
                icon: 'CheckIcon',
                action: this.completeChat
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

            const viewChatUser = {
                title: 'View Chat User',
                icon: 'UserIcon',
                disabled: () => !this.chat.chat_user_id,
                action: () => {
                    window.open(`/chat-users/${this.chat.chat_user_id}`, '_blank');
                }
            }

            if (this.historical) {
                if (this.showHistory) {
                    itemArray.push(hideHistory);
                } else {
                    itemArray.push(showHistory);
                }
                
                if (this.showDetails) {
                    itemArray.push(hideDetails);
                } else {
                    itemArray.push(showDetails);
                }

                itemArray.push(viewChatUser);
                // Add divider between

                return itemArray;
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

                if (this.showDetails) {
                    itemArray.push(hideDetails);
                } else {
                    itemArray.push(showDetails);
                }

                if (this.showHistory) {
                    itemArray.push(hideHistory);
                } else {
                    itemArray.push(showHistory);
                }

                itemArray.push(viewChatUser);

                return itemArray;
            }

            if (this.cobrowsing) {
                itemArray.push(stopCobrowse);
            } else {
                itemArray.push(requestCobrowse);
            }

            // If you are an agent in the list of agents
            itemArray.push(inviteAgent);
            if (this.showDetails) {
                itemArray.push(hideDetails);
            } else {
                itemArray.push(showDetails);
            }

            if (this.showHistory) {
                itemArray.push(hideHistory);
            } else {
                itemArray.push(showHistory);
            }

            itemArray.push(viewChatUser);
            itemArray.push(markAsComplete);
            itemArray.push({ type: 'divider' });
            itemArray.push(leaveChat);
            // itemArray.push(deleteChat);


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

        completeChat() {
            this.$modals.push('CompleteChatModal', {
                chat_id: this.chat.id,
                name: this.chat.name,
                hideCloseButton: true
            });
        },

        leaveChat() {
            this.$modals.push('LeaveChatModal', {
                chat_id: this.chat.id,
                name: this.chat.name,
                hideCloseButton: true
            });
        },

        deleteChat() {
            this.$modals.push('DeleteChatModal', {
                chat_id: this.chat.id,
                name: this.chat.name,
                hideCloseButton: true
            });
        },

        emitShowHistory() {
            EventHub.emit('chats:show-history');
        },

        emitHideHistory() {
            EventHub.emit('chats:hide-history');
        },

        emitShowDetails() {
            EventHub.emit('chats:show-details');
        },

        emiteHideDetails() {
            EventHub.emit('chats:hide-details');
        }
    }
}
</script>