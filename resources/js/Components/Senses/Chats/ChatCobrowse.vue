<template>
        <div
            class="flex flex-col flex-grow h-full overflow-y-hidden border-r border-zinc-200"
            style="height: calc(100vh - 128px); min-height: calc(100vh - 128px)"
        >
            <div class="p-3 border-b border-zinc-200 flex items-center justify-between bg-white">
                <div class="text-black font-semibold text-xl">Cobrowsing with {{ chat?.chat_user?.full_name }}</div>

                <ButtonGroup>
                    <SecondaryButton @click="togglePulseMode">{{inPulseMode ? 'Exit' : 'Enter'}} Pulse Mode</SecondaryButton>
                    <SecondaryButton @click="stopCobrowse">Stop</SecondaryButton>
                </ButtonGroup>
            </div>
            <div class="bg-white flex items-center justify-center" style="height: calc(100vh - 200px); max-height: calc(100vh - 200px)">
                <video 
                    class="w-auto border border-zinc-200 border-t-0" 
                    id="video"
                    autoplay
                    @click="sendPulse">
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
            peer: null,
            channel: null,

            inPulseMode: false,
        };
    },
    mounted() {
        this.setupEventHubListeners();
        this.setupEchoListeners();
    },
    beforeUnmount() {
        this.destroyEventHubListeners();
        this.destroyEchoListeners();
        this.stopCobrowse();
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

            this.channel = chatChannel;
        },
        destroyEchoListeners() {
            this.channel.stopListening('Chats\\Signal');
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

            this.peer.on('close', () => this.stopCobrowse());
        },
        stopCobrowse() {
            if (this.peer) {
                EventHub.emit('cobrowse:stop')
                this.peer.destroy();
                this.peer = null;
            }
        },

        // Pulses
        togglePulseMode() {
            this.inPulseMode = !this.inPulseMode;
        },
        sendPulse(e) {
            if (!this.inPulseMode) return;

            const rect = e.target.getBoundingClientRect();
const width = rect.width;
const height = rect.height;

const xPercentage = ((e.clientX - rect.left) / width) * 100;
const yPercentage = ((e.clientY - rect.top) / height) * 100;

            axios.post('/api/v2/pulse/chats', {
                chat_id: this.chat.id,
                x: xPercentage,
    y: yPercentage,
            });

            this.inPulseMode = false;
        }
    },
}
</script>
