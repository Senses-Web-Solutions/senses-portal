<template>
    <div
        class="h-full border-r border-zinc-200 overflow-y-scroll hide-scrollbar transition-all duration-500 ease-out"
        :class="minimisedClasses"
        style="max-height: calc(100vh - 128px); min-height: calc(100vh - 128px)"
    >
        <div class="p-4">
            <div class="flex justify-between items-center mb-3">
                <h1 class="text-xl font-bold text-black">Inbox</h1>
                <SecondaryButton size="xs" @click="toggleSidebar">
                    <ChevronDoubleLeftIcon class="'w-4 h-4 text-zinc-500 transition-all duration-500 transform" :class="rotationClasses"/>
                </SecondaryButton>
            </div>

            <SeInput
                v-model="searchQuery"
                id="search"
                name="search"
                placeholder="Search"
                class="!border-l-0 focus:!ring-0 focus:!border-zinc-300 !pl-0"
                @update:modelValue="searchChats"
            >
                <template #icon>
                    <SearchIcon class="w-5 h-5 text-zinc-300" />
                </template>
            </SeInput>
        </div>

        <div v-if="formattedChats && !loadingChats" class="h-full space-y-4">
            <ChatSidebarChatList
                v-for="(chats, type) in this.formattedChats"
                :key="type"
                :type="type"
                :chats="chats"
                :selected-chat="selectedChat"
                :empty-message="emptyMessages[type]"
                @select-chat="selectChat"
            />
        </div>

        <div v-if="loadingChats" class="flex items-center justify-center h-full">
            <LoadingIcon class="w-12 h-12 text-primary-600" />
        </div>
    </div>
</template>
<script>
import { debounce } from 'lodash-es';
import { ChevronDoubleLeftIcon, ChevronDoubleRightIcon, SearchIcon } from '@heroicons/vue/outline';

import SeInput from '../../Ui/Inputs/SeInput.vue';
import EmptyState from '../../Ui/EmptyState.vue';
import ChatSidebarChatList from './ChatSidebarChatList.vue';
import LoadingIcon from '../../Ui/LoadingIcon.vue';
import SecondaryButton from '../../Ui/Buttons/SecondaryButton.vue';

export default {
    components: {
        SeInput,
        EmptyState,
        SearchIcon,
        ChatSidebarChatList,
        LoadingIcon,
        SecondaryButton,
        ChevronDoubleLeftIcon,
        ChevronDoubleRightIcon
    },
    props: {
        chats: {
            type: Object,
            required: true
        },
        selectedChat: {
            type: Object,
            required: false
        },
        loadingChats: {
            type: Boolean,
            required: true
        }
    },
    emits: ['chatSelected'],
    data() {
        return {
            filteredChats: {
                assigned: [],
                new: [],
                'in progress': [],
                'invited': [],
            },

            searchQuery: '',
            emptyMessages: {
                assigned: 'You have are not assigned to any chats',
                new: 'There are no new chats',
                'in progress': 'There are no chats in progress',
                'invited': 'You have not been invited to any chats',
            },

            minimised: false,
        }
    },
    computed: {
        assignedChats() {
            return this.getChats('assigned') ?? [];
        },

        unassignedChats() {
            return this.getChats('unassigned') ?? [];
        },

        newChats() {
            return this.getChats('new') ?? [];
        },

        inProgressChats() {
            return this.getChats('in progress') ?? [];
        },

        invitedChats() {
            return this.getChats('invited') ?? [];
        },

        formattedChats() {
            return {
                assigned: this.assignedChats,
                unassigned: this.unassignedChats,
                new: this.newChats,
                'in progress': this.inProgressChats,
                'invited': this.invitedChats,
            };
        },

        totalChats() {
            return this.newChats.length + this.assignedChats.length;
        },

        minimisedClasses() {
            return {
                'w-1/6 min-w-52 max-w-60': this.minimised,
                'w-1/5 min-w-80 max-w-96': !this.minimised,
            }
        },

        rotationClasses() {
            return this.minimised ? 'rotate-180' : 'rotate-0';
        }
    },
    methods: {
        getChats(type) {
            if (this.searchQuery && this.filteredChats[type].length === 0) {
                this.emptyMessages[type] = `No "${type.charAt(0).toUpperCase() + type.slice(1)}" chats found for "${this.searchQuery}"`;
                return [];
            }

            return this.filteredChats[type]?.length > 0 ? this.filteredChats[type] : this.chats[type];
        },
        searchChats: debounce(function() {
            ['new', 'assigned'].forEach(type => {
                this.filteredChats[type] = this.chats[type].filter(chat => {
                    return chat.name.toLowerCase().includes(this.searchQuery.toLowerCase());
                });
            });
        }, 250),

        selectChat(chat) {
            this.$emit('chatSelected', chat);
        },

        toggleSidebar() {
            this.minimised = !this.minimised;
        }
    }
}
</script>
<style>
.hide-scrollbar::-webkit-scrollbar {
    display: none;
}

.hide-scrollbar {
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
}
</style>