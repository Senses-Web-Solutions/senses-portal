<template>
    <!-- Inbox has three columns -->
        <div class="flex w-full h-full" style="min-height: calc(100vh - 132px);">
            <ChatInboxSidebar :chats="chats" :selected-chat="selectedChat" @chatSelected="(chat) => selectedChat = chat"/>

            <ChatInboxMessenger :chat="selectedChat" />
        </div>
</template>
<script>
import axios from 'axios';

import ChatInboxSidebar from './ChatInboxSidebar.vue';
import ChatInboxMessenger from './ChatInboxMessenger.vue';

import EventHub from '../../../Support/EventHub';

export default {
    components: {
        ChatInboxSidebar,
        ChatInboxMessenger,
    },
    props: {
        url: {
            type: String,
            default: '/api/v2/inbox/chats'
        }
    },
    data() {
        return {
            chats: {
                new: [],
            },

            selectedChat: null,
            loadingChats: true,
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
    mounted() {
        this.fetchChats();

        EventHub.on('chats:fetch', this.fetchChats)
    },
    beforeUnmount() {
        EventHub.off('chats:fetch')
    },
    methods: {
        fetchChats() {
            this.loadingChats = true;

            axios.get(this.url)
                .then(response => {
                    this.chats = response.data;
                })
                .catch(error => {
                    console.log(error)
                })
                .finally(() => {
                    this.loadingChats = false;
                })
        },
    }
}
</script>