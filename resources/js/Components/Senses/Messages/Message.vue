<template>
    <div
        class="w-80 p-3 relative shadow rounded-lg text-black space-y-1"
        :class="{ 
            'bg-zinc-200 ml-auto' : message.from_agent, 
            'bg-primary-200': !message.from_agent,
            'mt-1': inChain,
            'mt-3': !inChain }"
            :id="'message-' + message.id"
    >
        <p v-if="!inChain" class="font-semibold">{{ message.author }}</p>

        <div v-html="message.content" class="message_content" @click="handleClick"></div>
        
        <MessageReadStatus :message="message" />
    </div>
</template>
<script>
import axios from 'axios';

import MessageReadStatus from './MessageReadStatus.vue';

export default {
    components: {
        MessageReadStatus,
    },
    props: {
        message: {
            type: Object,
            required: true,
        },
        inChain: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            observer: null,
        };
    },
    mounted() {
        this.setupObserver();
    },
    beforeUnmount() {
        if (this.observer) {
            this.observer.disconnect();
        }
    },
    methods: {
        setupObserver() {
            if (this.message.from_agent || this.message.read_at) return;

            this.observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                if (entry.isIntersecting && document.visibilityState === "visible") {

                    const id = entry.target.id.split("-")[1];

                    axios.get(`/api/v2/messages/${id}/read`).then((response) => {
                    if (response.data?.read_at) {
                        // Remove this entry from the observer
                        this.observer.unobserve(entry.target);
                    }
                    });
                }
                });
            });

            this.observer.observe(this.$el);
        },
        handleClick(e) {
            // If target is image, open in new tab
            if (e.target.tagName === 'IMG') {
                let imageSrc = e.target.src;
                if (imageSrc.startsWith('data:image')) {
                    // Create a Blob from the base64 string
                    let byteCharacters = atob(imageSrc.split(',')[1]);
                    let byteNumbers = new Array(byteCharacters.length);
                    for (let i = 0; i < byteCharacters.length; i++) {
                        byteNumbers[i] = byteCharacters.charCodeAt(i);
                    }
                    let byteArray = new Uint8Array(byteNumbers);
                    let blob = new Blob([byteArray], {type: 'image/jpeg'}); // Change 'image/jpeg' to the actual type of the image

                    // Create an Object URL from the Blob and open it in a new tab
                    let objectUrl = URL.createObjectURL(blob);
                    window.open(objectUrl, '_blank');
                } else {
                    window.open(imageSrc, '_blank');
                }
            }
        },
    },
};
</script>
<style>
.message_content img {
    border-radius: 0.5rem;
    cursor: pointer;
}
</style>