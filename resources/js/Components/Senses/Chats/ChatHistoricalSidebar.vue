<template>
    <div
        class="h-full border-r border-zinc-200 overflow-y-scroll hide-scrollbar transition-all duration-500 ease-out"
        :class="minimisedClasses"
        style="max-height: calc(100vh - 128px); min-height: calc(100vh - 128px)"
    >
        <div class="flex justify-between gap-3 items-center p-3">
            <h1 class="text-xl font-bold text-black">History</h1>

            <SeInput
                v-model="searchQuery"
                id="search"
                name="search"
                placeholder="Search"
                class="!border-l-0 focus:!ring-0 focus:!border-zinc-300 !pl-0 !min-h-8"
                @update:modelValue="searchChats"
            >
                <template #icon>
                    <SearchIcon class="w-5 h-5 text-zinc-300" />
                </template>
            </SeInput>

            <SecondaryButton size="xs" @click="toggleSidebar">
                <ChevronDoubleLeftIcon
                    class="'w-4 h-4 text-zinc-500 transition-all duration-500 transform"
                    :class="rotationClasses"
                />
            </SecondaryButton>
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

        <div
            v-if="loadingChats"
            class="flex items-center justify-center h-full"
        >
            <LoadingIcon class="w-12 h-12 text-primary-600" />
        </div>
    </div>
</template>
<script>
import { debounce } from "lodash-es";
import {
    ChevronDoubleLeftIcon,
    ChevronDoubleRightIcon,
    SearchIcon,
} from "@heroicons/vue/outline";

import SeInput from "../../Ui/Inputs/SeInput.vue";
import EmptyState from "../../Ui/EmptyState.vue";
import ChatSidebarChatList from "./ChatSidebarChatList.vue";
import LoadingIcon from "../../Ui/LoadingIcon.vue";
import SecondaryButton from "../../Ui/Buttons/SecondaryButton.vue";

export default {
    components: {
        SeInput,
        EmptyState,
        SearchIcon,
        ChatSidebarChatList,
        LoadingIcon,
        SecondaryButton,
        ChevronDoubleLeftIcon,
        ChevronDoubleRightIcon,
    },
    props: {
        chats: {
            type: Object,
            required: true,
        },
        selectedChat: {
            type: Object,
            required: false,
        },
        loadingChats: {
            type: Boolean,
            required: true,
        },
    },
    emits: ["chatSelected"],
    data() {
        return {
            filteredChats: {
                chats: [],
            },

            searchQuery: "",
            emptyMessages: {
                chats: "There are no historical chats.",
            },

            minimised: false,
        };
    },
    computed: {
        formattedChats() {
            return {
                chats:
                    this.filteredChats["chats"].length > 0
                        ? this.filteredChats["chats"]
                        : this.chats,
            };
        },

        totalChats() {
            return this.newChats.length + this.assignedChats.length;
        },

        minimisedClasses() {
            return {
                "w-1/6 min-w-52 max-w-60": this.minimised,
                "w-1/5 min-w-80 max-w-96": !this.minimised,
            };
        },

        rotationClasses() {
            return this.minimised ? "rotate-180" : "rotate-0";
        },
    },
    methods: {
        searchChats: debounce(function () {
            this.filteredChats["chats"] = this.chats.filter((chat) => {
                return chat?.chat_user?.full_name
                    .toLowerCase()
                    .includes(this.searchQuery.toLowerCase());
            });
        }, 250),

        selectChat(chat) {
            this.$emit("chatSelected", chat);
        },

        toggleSidebar() {
            this.minimised = !this.minimised;
        },
    },
};
</script>
<style>
.hide-scrollbar::-webkit-scrollbar {
    display: none;
}

.hide-scrollbar {
    -ms-overflow-style: none; /* IE and Edge */
    scrollbar-width: none; /* Firefox */
}
</style>
