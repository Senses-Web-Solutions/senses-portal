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
        <p>{{ message.content }}</p>
        <div class="flex justify-end items-center select-none">
            <p class="text-sm mr-1">{{ FormatTime(message.sent_at, 'HH:mm') }}</p>
            <div v-if="message.from_agent" class="leading-none last:mr-0 text-gray-500 z-10" :class="{ '!text-blue-500': message.sent_at }">
                <CheckIcon class="h-4 w-4" />
            </div>
            <div v-if="message.from_agent" class="leading-none text-gray-500 -ml-3 z-0" :class="{ '!text-blue-500': message.read_at }">
                <CheckIcon class="h-4 w-4" />
            </div>
        </div>
    </div>
</template>
<script>
import axios from 'axios';
import { CheckIcon } from '@heroicons/vue/outline';

import FormatTime from '../../../Filters/FormatTime';

export default {
    components: {
        CheckIcon,
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
        FormatTime,

        setupObserver() {
            if (this.message.from_agent || this.message.read_at) return;

            this.observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                if (entry.isIntersecting && document.visibilityState === "visible") {
                    console.log(`Message ${entry.target.id} has been read`);

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
        }
    },
};
</script>