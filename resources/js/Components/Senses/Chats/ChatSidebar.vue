<template>
    <div
        class="w-1/5 min-w-80 max-w-96 h-full border-r border-zinc-200 overflow-y-scroll hide-scrollbar"
        style="max-height: calc(100vh - 128px); min-height: calc(100vh - 128px)"
    >
        <div class="p-4">
            <div class="flex justify-between">
                <h1 class="text-xl font-bold text-black mb-3">Inbox</h1>
                <h1 class="text-xl font-bold text-black mb-3">{{ totalChats }}</h1>
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
import { SearchIcon } from '@heroicons/vue/outline';

import SeInput from '../../Ui/Inputs/SeInput.vue';
import EmptyState from '../../Ui/EmptyState.vue';
import ChatSidebarChatList from './ChatSidebarChatList.vue';
import LoadingIcon from '../../Ui/LoadingIcon.vue';

export default {
    components: {
        SeInput,
        EmptyState,
        SearchIcon,
        ChatSidebarChatList,
        LoadingIcon
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
            },

            searchQuery: '',
            emptyMessages: {
                assigned: 'You have no chats assigned to you',
                new: 'There are no new chats',
            }
        }
    },
    computed: {
        assignedChats() {
            return this.getChats('assigned') ?? [];
        },

        newChats() {
            return this.getChats('new') ?? [];
        },

        formattedChats() {
            return {
                assigned: this.assignedChats,
                new: this.newChats,
            };
        },

        totalChats() {
            return this.newChats.length + this.assignedChats.length;
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