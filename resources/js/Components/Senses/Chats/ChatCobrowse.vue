<template>
    <div
        class="flex flex-col flex-grow h-full overflow-y-scroll"
        style="height: calc(100vh - 128px); min-height: calc(100vh - 128px)"
    >
        <div class="p-3 border-b border-zinc-200 flex items-center">
            <div class="text-black font-semibold text-xl">Cobrowsing</div>
        </div>
        <div class="h-full">
            <video class="h-full w-auto" ref="video" autoplay></video>
        </div>
    </div>
</template>
<script>
import axios from 'axios';
import Peer from "simple-peer/simplepeer.min.js";

import useEcho from '../../../Support/useEcho';
import EventHub from '../../../Support/EventHub';

const echo = useEcho();

export default {
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
            EventHub.on('cobrowsing:start', () => this.setupPeer());
        },
        destroyEventHubListeners() {
            EventHub.off('cobrowsing:start', () => this.setupPeer());
        },
        setupEchoListeners() {
            const chatChannel = echo.private(`companies.${user().company_id}.chat`);

            // Equivalent to this.peer1.on("signal", data => { // Move to Portal
            chatChannel.listen('Chats\\Signal', (data) => {
                if (!data.fromAgent) {
                    console.log('Recieved Signal', data);
                    this.peer.signal(data.data);
                }
            });
        },
        setupPeer() {
            this.peer = new Peer();

            this.peer.on("signal", data => { // Move to Portal
                // Hit portal endpoint to send echo message to peer1
                // this.peer1.signal(data);

                console.log('Sending Signal', data);
                axios.post('/api/v2/signal', {
                    chat_id: this.chat.id,
                    data: data
                })
            });

            this.peer.on('stream', stream => { // Move to Portal
                console.log('Stream Received', stream);

                const video = this.$refs.video;

                if ('srcObject' in video) {
                    video.srcObject = stream;
                } else {
                    video.src = window.URL.createObjectURL(stream);
                }
            });
        },
    },
}
</script>
