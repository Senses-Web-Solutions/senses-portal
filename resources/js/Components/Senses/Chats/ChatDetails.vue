<template>
    <div class="border-zinc-200 transition-width duration-500 bg-white" :class="showClasses">
        <div class="flex items-center justify-between p-3 border-b border-zinc-200" v-if="show">
            <h1 class="text-xl font-bold text-black">Details</h1>
            <SecondaryButton @click="hide">Hide</SecondaryButton>
        </div>

        <div v-if="show && chat" class="w-full relative overflow-y-hidden bg-white p-3">
            <div class="flex flex-col space-y-4">
                <div class="flex justify-between">
                    <SeLabel>Chat ID:</SeLabel>
                    <p class="text-black">{{ chat?.id }}</p>
                </div>

                <div v-if="chat?.chat_user" class="flex justify-between">
                    <SeLabel>User:</SeLabel>
                    <!-- <UserPopover
                        panel-class="origin-top-left left-0"
                        :id="agent?.id"
                        :title="agent?.full_name"
                    > -->
                        <Tag
                            highlight-border
                            :highlight-dot="false"
                            colour="purple"
                            :href="`/chat-users/${chat.chat_user?.id}`"
                        >
                            {{ chat.chat_user?.full_name }}
                        </Tag>
                    <!-- </UserPopover> -->
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

                <div v-if="chat?.device_resolution" class="flex justify-between">
                    <SeLabel>Device Resolution:</SeLabel>
                    <p class="text-black">{{ chat.device_resolution }}</p>
                </div>

                <div v-if="chat?.tab_resolution" class="flex justify-between">
                    <SeLabel>Tab Resolution:</SeLabel>
                    <p class="text-black">{{ chat.tab_resolution }}</p>
                </div>

                <div v-if="chat?.browser" class="flex justify-between">
                    <SeLabel>Browser:</SeLabel>
                    <p class="text-black">{{ chat.browser }}</p>
                </div>

                <div v-if="chat?.browser_version" class="flex justify-between">
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
                    <img class="h-5 w-10" :src="`/images/flags/${chat.country_code.toUpperCase()}.svg`">
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
</template>
<script>
import ChatActionLog from '../ActionLogs/Chats/ChatActionLog.vue';
import SecondaryButton from '../../Ui/Buttons/SecondaryButton.vue';
import EmptyState from '../../Ui/EmptyState.vue';
import BasicMap from '../Maps/BasicMap.vue';
import SeLabel from '../../Ui/Inputs/SeLabel.vue';
import Tag from '../../Ui/Tags/Tag.vue';

import EventHub from '../../../Support/EventHub';
import MapHelpers from '../../../Support/Map/MapHelpers';

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
            required: true
        },
        show: {
            type: Boolean,
            required: true
        }
    },
    data() {
        return {
            actionLogs: [],
            markers: [],
            showMap: false,
        }
    },
    computed: {
        showClasses() {
            return {
                'w-0 translate-x-full p-0': !this.show,
                'w-96 border-l': this.show,
            }
        },
    },
    watch: {
        chat() {
            if (this.chat && this.chat?.geom) {
                const marker = this.setupMarkerDataFromCoordinates([this.chat.geom.x, this.chat.geom.y]);
                this.markers = [marker];
            } 
        },
        show(newVal) {
            if (newVal) {
                setTimeout(() => {
                    this.showMap = true;
                }, 500);
            }
        }
    },
    methods: {
        hide() {
            EventHub.emit('chats:hide-details');
        },
    },
}
</script>
<style>
.transition-width {
    transition-property: width;
}
</style>