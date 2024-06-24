<template>
        <div
            class="flex flex-col flex-grow h-full overflow-y-hidden border-r border-zinc-200"
            style="height: calc(100vh - 128px); min-height: calc(100vh - 128px)"
        >
            <div class="p-3 border-b border-zinc-200 flex items-center justify-between">
                <div class="text-black font-semibold text-xl">Cobrowsing with {{ chat.name }}</div>

                <ButtonGroup>
                    <SecondaryButton @click="stopCobrowse">Stop</SecondaryButton>
                </ButtonGroup>
            </div>
            <div class="h-full">
                <video 
                    style="height: calc(100vh - 170px); min-height: calc(100vh - 170px)"
                    class="w-auto" 
                    id="video"
                    autoplay>
                </video>
            </div>
        </div>
</template>
<script>
import axios from 'axios';
import Peer from "simple-peer/simplepeer.min.js";

import ButtonGroup from '../../Ui/Buttons/ButtonGroup.vue';
import SecondaryButton from '../../Ui/Buttons/SecondaryButton.vue';

import useEcho from '../../../Support/useEcho';
import EventHub from '../../../Support/EventHub';

const echo = useEcho();

export default {
    components: {
        ButtonGroup,
        SecondaryButton,
    },
    props: {
        chat: {
            type: Object,
            required: true,
        },
        cobrowsing: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            peer: null
        };
    },
    mounted() {
        this.setupEventHubListeners();
        this.setupEchoListeners();
    },
    beforeUnmount() {
        this.destroyEventHubListeners();
    },
    methods: {
        setupEventHubListeners() {
            EventHub.on('cobrowse:start', () => this.setupPeer());
        },
        destroyEventHubListeners() {
            EventHub.off('cobrowse:start', () => this.setupPeer());
        },
        setupEchoListeners() {
            const chatChannel = echo.private(`companies.${user().company_id}.chat`);

            // Equivalent to this.peer1.on("signal", data => {
            chatChannel.listen('Chats\\Signal', (data) => {
                if (!data.fromAgent) {
                    this.peer.signal(data.data);
                }
            });
        },
        setupPeer() {
            this.peer = new Peer();

            this.peer.on("signal", data => {
                axios.post('/api/v2/signal', {
                    chat_id: this.chat.id,
                    data: data
                })
            });

            this.peer.on('stream', stream => {
                const video = document.getElementById('video');

                if (video !== null) {
                    video.srcObject = stream;
                }
            });

            this.peer.on('close', () => EventHub.emit('cobrowse:stop'));
        },
        stopCobrowse() {
            this.peer.destroy();
        }
    },
}
</script>
