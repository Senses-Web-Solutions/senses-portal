<template>
    <div
        class="border-zinc-200 transition-width duration-500 bg-white overflow-y-auto"
        :class="showClasses"
    >
        <div
            class="flex items-center justify-between p-3 border-b border-zinc-200"
            v-if="show"
        >
            <h1 class="text-xl font-bold text-black">Details</h1>
            <SecondaryButton @click="hide">Hide</SecondaryButton>
        </div>

        <div
            v-if="show && chat"
            class="w-full relative overflow-y-auto bg-white p-3 space-y-8"
        >
            <div>
                <h2 class="text-black text-lg font-semibold mb-4">
                    Defined Meta
                </h2>
                <div v-if="chat?.meta" class="flex flex-col space-y-4">
                    <div
                        v-for="(meta, key) in filteredDefinedMeta"
                        class="flex justify-between"
                    >
                        <SeLabel>{{ key }}:</SeLabel>

                        <div class="flex gap-2">
                            <Tag
                                v-for="(item, index) in meta"
                                highlight-border
                                :highlight-dot="false"
                                colour="purple"
                                v-if="Array.isArray(meta)"
                                class="text-black"
                            >
                                <span>
                                    {{ item[`${chat.meta[`${key}_key`]}`] }}
                                </span>
                            </Tag>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <h2 class="text-black text-lg font-semibold mb-4">
                    Default Meta
                </h2>
                <div class="flex flex-col space-y-4">
                    <div class="flex justify-between">
                        <SeLabel>Chat ID:</SeLabel>
                        <p class="text-black">{{ chat?.id }}</p>
                    </div>

                    <div v-if="chat?.system" class="flex justify-between">
                        <SeLabel>System:</SeLabel>
                        <a :href="chat?.system" target="_blank" class="text-primary-500 underline hover:text-primary-700">{{ chat?.system }}</a>
                    </div>

                    <div v-if="chat?.current_page" class="flex justify-between">
                        <SeLabel>Page:</SeLabel>
                        <a :href="chat?.current_page" target="_blank" class="text-primary-500 underline hover:text-primary-700">{{ chat?.current_page }}</a>
                    </div>

                    <div v-if="chat?.chat_user?.external_id" class="flex justify-between">
                        <SeLabel>External User ID:</SeLabel>
                        <p class="text-black">
                            {{ chat.chat_user?.external_id }}
                        </p>
                    </div>

                    <div v-if="chat?.chat_user" class="flex justify-between">
                        <SeLabel>User:</SeLabel>
                        <Tag
                            highlight-border
                            :highlight-dot="false"
                            colour="purple"
                            :href="`/chat-users/${chat.chat_user?.id}`"
                        >
                            {{ chat.chat_user?.full_name }}
                        </Tag>
                    </div>

                    <div v-if="chat?.ip" class="flex justify-between">
                        <SeLabel>IP Address:</SeLabel>
                        <p class="text-black">{{ chat?.ip }}</p>
                    </div>

                    <div v-if="chat?.language" class="flex justify-between">
                        <SeLabel>Language:</SeLabel>
                        <p class="text-black">{{ chat.language }}</p>
                    </div>

                    <div v-if="chat?.timezone" class="flex justify-between">
                        <SeLabel>Timezone:</SeLabel>
                        <p class="text-black">{{ chat.timezone }}</p>
                    </div>

                    <div
                        v-if="chat?.device_resolution"
                        class="flex justify-between"
                    >
                        <SeLabel>Device Resolution:</SeLabel>
                        <p class="text-black">{{ chat.device_resolution }}</p>
                    </div>

                    <div
                        v-if="chat?.tab_resolution"
                        class="flex justify-between"
                    >
                        <SeLabel>Tab Resolution:</SeLabel>
                        <p class="text-black">{{ chat.tab_resolution }}</p>
                    </div>

                    <div v-if="chat?.browser" class="flex justify-between">
                        <SeLabel>Browser:</SeLabel>
                        <p class="text-black">{{ chat.browser }}</p>
                    </div>

                    <div
                        v-if="chat?.browser_version"
                        class="flex justify-between"
                    >
                        <SeLabel>Browser Version:</SeLabel>
                        <p class="text-black">{{ chat.browser_version }}</p>
                    </div>

                    <div v-if="chat?.os" class="flex justify-between">
                        <SeLabel>OS:</SeLabel>
                        <p class="text-black">{{ chat.os }}</p>
                    </div>

                    <div v-if="chat?.os_version" class="flex justify-between">
                        <SeLabel>OS Version:</SeLabel>
                        <p class="text-black">{{ chat.os_version }}</p>
                    </div>

                    <div v-if="chat?.device" class="flex justify-between">
                        <SeLabel>Device:</SeLabel>
                        <p class="text-black">{{ chat.device }}</p>
                    </div>

                    <div v-if="chat?.geom" class="flex justify-between">
                        <SeLabel>Longitude:</SeLabel>
                        <p class="text-black">{{ chat.geom.x }}</p>
                    </div>

                    <div v-if="chat?.geom" class="flex justify-between">
                        <SeLabel>Latitude:</SeLabel>
                        <p class="text-black">{{ chat.geom.y }}</p>
                    </div>

                    <div v-if="chat?.country_code" class="flex justify-between">
                        <SeLabel>Country:</SeLabel>
                        <img
                            class="h-5 w-10"
                            :src="`/images/flags/${chat.country_code.toUpperCase()}.svg`"
                        />
                    </div>

                    <div v-if="showMap && markers?.length > 0" class="relative">
                        <SeLabel class="mb-2">Location:</SeLabel>
                        <BasicMap
                            :show-controls="false"
                            :zoom="15"
                            :center="markers[0]?.coordinates"
                            :markers="markers"
                            class="h-56 rounded-lg shadow overflow-hidden"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import ChatActionLog from "../ActionLogs/Chats/ChatActionLog.vue";
import SecondaryButton from "../../Ui/Buttons/SecondaryButton.vue";
import EmptyState from "../../Ui/EmptyState.vue";
import BasicMap from "../Maps/BasicMap.vue";
import SeLabel from "../../Ui/Inputs/SeLabel.vue";
import Tag from "../../Ui/Tags/Tag.vue";

import EventHub from "../../../Support/EventHub";
import MapHelpers from "../../../Support/Map/MapHelpers";

export default {
    components: {
        SeLabel,
        ChatActionLog,
        SecondaryButton,
        EmptyState,
        BasicMap,
        Tag,
    },
    mixins: [MapHelpers],
    props: {
        chat: {
            type: [Object, null],
            required: true,
        },
        show: {
            type: Boolean,
            required: true,
        },
    },
    data() {
        return {
            actionLogs: [],
            markers: [],
            showMap: false,
        };
    },
    computed: {
        showClasses() {
            return {
                "w-0 translate-x-full p-0": !this.show,
                "w-96 border-l": this.show,
            };
        },
        filteredDefinedMeta() {
            return Object.fromEntries(
                Object.entries(this.chat.meta).filter(
                    ([key, value]) => !key.includes("_key")
                )
            );
        },
    },
    watch: {
        chat() {
            if (this.chat && this.chat?.geom) {
                const marker = this.setupMarkerDataFromCoordinates([
                    this.chat.geom.x,
                    this.chat.geom.y,
                ]);
                this.markers = [marker];
            }
        },
        show(newVal) {
            if (newVal) {
                setTimeout(() => {
                    this.showMap = true;
                }, 500);
            }
        },
    },
    methods: {
        hide() {
            EventHub.emit("chats:hide-details");
        },
    },
};
</script>
<style>
.transition-width {
    transition-property: width;
}
</style>
