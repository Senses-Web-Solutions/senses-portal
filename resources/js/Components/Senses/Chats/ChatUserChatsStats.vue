<template>
    <section>
        <div class="flex items-center px-2 pb-4 lg:space-x-3">
            <div>
                <SectionTitle> Chat Stats </SectionTitle>
            </div>
        </div>
        <div class="grid grid-cols-4 gap-8">
            <Card
                v-for="(stat, key) in stats"
                :key="key"
                :external-title="false"
                flush
            >
                <div
                    class="relative overflow-hidden rounded-lg bg-white p-6 shadow"
                >
                    <dt>
                        <div
                            class="absolute rounded-md p-3"
                            :class="colour(key, stat.value, 'bg')"
                        >
                            <component
                                :is="stat.icon"
                                class="h-6 w-6 text-white"
                            />
                        </div>
                        <p class="ml-16 font-medium text-zinc-500 capitalize">
                            {{ stat.title }}
                        </p>
                    </dt>
                    <dd class="ml-16 flex items-baseline">
                        <p
                            class="text-2xl font-semibold"
                            :class="colour(key, stat.value, 'text')"
                        >
                            {{ key === 'duration' ? hrDuration(stat.value) : stat.value }}
                        </p>
                    </dd>
                </div>
            </Card>
        </div>
    </section>
</template>
<script>
import axios from "axios";
import {
    XCircleIcon,
    CheckCircleIcon,
    ClockIcon,
    ChatIcon,

} from "@heroicons/vue/outline";

import SectionTitle from "../../Ui/Sections/SectionTitle.vue";
import ButtonGroup from "../../Ui/Buttons/ButtonGroup.vue";
import Card from "../../Ui/Cards/Card.vue";
import EmptyState from "../../Ui/EmptyState.vue";

export default {
    components: {
        SectionTitle,
        ButtonGroup,
        Card,
        EmptyState,
        XCircleIcon,
        CheckCircleIcon,
        ClockIcon,
        ChatIcon,
    },
    props: {
        id: {
            type: Number,
            required: true,
        },
    },
    data() {
        return {
            stats: {
                chats: {
                    title: `Chats`,
                    icon: "ChatIcon",
                    value: 0,
                },
                duration: {
                    title: "Avg. Duration",
                    icon: "ClockIcon",
                    value: 0,
                },
                resolved: {
                    title: "Resolved",
                    icon: "CheckCircleIcon",
                    value: 0,
                },
                unresolved: {
                    title: "Unresolved",
                    icon: "XCircleIcon",
                    value: 0,
                },
            },
            completedChats: 0,
        };
    },
    mounted() {
        this.fetchStats();
    },
    methods: {
        fetchStats() {
            axios
                .get(`/api/v2/chat-users/${this.id}/chats/stats`)
                .then((response) => {
                    if (response.data) {
                        const keys = Object.keys(response.data);
                        keys.forEach((key) => {
                            if (key === 'completed_chats') {
                                this.completedChats = response.data[key];
                            } else {
                                this.stats[key].value = response.data[key];
                            }
                        });
                    }
                })
                .catch((error) => {
                    console.error(error);
                });
        },

        colour(key, value, type) {
            switch (key) {
                case 'chats':
                    return this.avgMessagesSentColour(value, type);
                case 'duration':
                    return this.avgDurationColour(value, type);
                case 'resolved':
                    return this.resolvedColour(value, type);
                case 'unresolved':
                    return this.unresolvedColour(value, type);
                default:
                    return ''; // Default case if the key doesn't match any criteria
            }
        },

        avgMessagesSentColour(value, type) {
            if (this.completedChats === 0) return `${type}-zinc-600 dark:${type}-zinc-400`;

            if (value > 1) return `${type}-primary-600 dark:${type}-primary-400`;
        },

        avgDurationColour(value, type) {
            if (this.completedChats === 0) return `${type}-zinc-600 dark:${type}-zinc-400`;

            if (value < 300) return `${type}-primary-600 dark:${type}-primary-400`;
            else if (value < 450) return `${type}-green-600 dark:${type}-green-400`;
            else if (value < 600) return `${type}-yellow-600 dark:${type}-yellow-400`;
            else if (value < 900) return `${type}-orange-600 dark:${type}-orange-400`;
            else return `${type}-red-600 dark:${type}-red-400`;
        },

        resolvedColour(resolved, type) {
            if (this.completedChats === 0) return `${type}-zinc-600 dark:${type}-zinc-400`;

            if (resolved === 0 && this.completedChats > 0)
                return `${type}-red-600 dark:${type}-red-400`;
            else return `${type}-green-600 dark:${type}-green-400`;
        },

        unresolvedColour(unresolved, type) {
            if (this.completedChats === 0) return `${type}-zinc-600 dark:${type}-zinc-400`;
            
            if (unresolved === 0 && this.completedChats > 0)
                return `${type}-green-600 dark:${type}-green-400`;
            else return `${type}-red-600 dark:${type}-red-400`;
        },

        hrDuration(duration) {
            // Create a new Date object at the Unix epoch in UTC
            let helperDate = new Date(0);
            // Use UTC methods to add seconds
            helperDate.setUTCSeconds(duration);
            // Format the date in HH:mm:ss format using UTC methods
            const hours = helperDate.getUTCHours().toString().padStart(2, '0');
            const minutes = helperDate.getUTCMinutes().toString().padStart(2, '0');
            const seconds = helperDate.getUTCSeconds().toString().padStart(2, '0');
            return `${hours}:${minutes}:${seconds}`;
        }
    },
};
</script>
