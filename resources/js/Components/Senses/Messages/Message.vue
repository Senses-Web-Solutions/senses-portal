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

        <!-- Message files -->
         <!-- Say how many attachments are on message -->
         <div class="flex flex-col space-y-1 !mt-3 flex-wrap items-start" v-if="message.files?.length > 0">
            <div v-if="message.files.length > 0" class="text-sm text-gray-500">
                <span v-if="message.files.length === 1">1 Attachment</span>
                <span v-else>{{ message.files.length }} Attachments</span>
            </div>

            <div class="flex flex-wrap items-start">
                <File class="mb-2 mr-2" v-for="file in message.files" :key="file.id" :file="file" :files="message.files" />
            </div>
        </div>
        
        <MessageReadStatus :message="message" />
    </div>
</template>
<script>
import axios from 'axios';

import MessageReadStatus from './MessageReadStatus.vue';
import File from '../Files/File.vue';

export default {
    components: {
        MessageReadStatus,
        File
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