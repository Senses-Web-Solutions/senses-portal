<template>
    <div
        class="w-1/5 min-w-80 max-w-96 h-full border-r border-zinc-200"
        style="min-height: calc(100vh - 132px)"
    >
        <div class="p-4">
            <div>
                <h1 class="text-lg font-bold text-black mb-3">Inbox</h1>

                <!-- Show 3 small badges, how many you are in, how many are unassigned and total -->
            </div>

            <SeInput
                v-model="searchQuery"
                id="search"
                name="search"
                placeholder="Search"
                class="!border-l-0"
                @update:modelValue="searchChats"
            >
                <template #icon>
                    <SearchIcon class="w-5 h-5 text-zinc-300" />
                </template>
            </SeInput>
        </div>

        <div class="h-full divide-y divide-zinc-200">
            <div
                v-for="newChat in newChats"
                :key="newChat.id"
                class="flex gap-2 p-4 cursor-pointer transition-all border hover:bg-zinc-200"
                :class="{
                    'bg-zinc-200 !border !border-primary-400': selectedChat && selectedChat.id === newChat.id,
                    'border-transparent': selectedChat && selectedChat.id !== newChat.id
                }"
                @click="selectChat(newChat)"
            >
                <div
                    class="block h-8 w-8 rounded-full bg-primary-400 flex justify-center items-center text-white shrink-0"
                >
                    JD
                </div>

                <div class="w-full">
                    <div class="flex justify-between mb-1 w-full">
                        <h4 class="text-black font-semibold">
                            {{ newChat.name }}
                        </h4>
                        <p class="text-zinc-400 text-sm">14 mins ago</p>
                    </div>
                    <div class="flex justify-between w-full">
                        <p class="text-zinc-500">
                            {{ truncateMessage(newChat.last_message.content) }}
                        </p>
                        <div
                            class="h-5 w-5 bg-primary-400 rounded-full flex justify-center items-center text-white text-xs"
                        >
                            {{ newChat.unread_messages_count }}
                        </div>
                    </div>
                </div>
            </div>

            <EmptyState v-if="newChats.length === 0">
                {{ newEmptyMessage }}
            </EmptyState>
        </div>
    </div>
</template>
<script>
import { debounce } from 'lodash-es';
import { SearchIcon } from '@heroicons/vue/outline';

import SeInput from '../../Ui/Inputs/SeInput.vue';
import EmptyState from '../../Ui/EmptyState.vue';

export default {
    props: {
        chats: {
            type: Object,
            required: true
        },
        selectedChat: {
            type: Object,
            required: false
        }
    },
    components: {
        SeInput,
        EmptyState,
        SearchIcon,
    },
    emits: ['chatSelected'],
    data() {
        return {
            filteredChats: {
                new: [],
            },

            searchQuery: '',
            loadingChats: true,
            loadingState: true,
            newEmptyMessage: 'There are no "Incoming" chats... for now 👀'
        }
    },
    computed: {
        newChats() {
            if (this.searchQuery && this.filteredChats.new.length === 0) {
                this.newEmptyMessage = `No "Incoming" chats found for "${this.searchQuery}"`;
                return [];
            }

            return this.filteredChats?.new?.length > 0 ? this.filteredChats.new : this.chats.new;
        },
    },
    methods: {
        searchChats: debounce(function() {
            // Filter each set of chats by the search query
            this.filteredChats.new = this.chats.new.filter(chat => {
                return chat.name.toLowerCase().includes(this.searchQuery.toLowerCase());
            });
        }, 250),

        truncateMessage(message) {
            return message.length > 30 ? `${message.substring(0, 30)}...` : message;
        },

        selectChat(chat) {
            this.$emit('chatSelected', chat);
        }
    }
}
</script>