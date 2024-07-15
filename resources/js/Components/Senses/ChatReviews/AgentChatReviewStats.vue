<template>
    <section class="mb-12">
        <div class="flex items-center px-2 pb-4 lg:space-x-3">
            <div>
                <SectionTitle> Chat Review Stats </SectionTitle>
            </div>
        </div>
        <div class="grid grid-cols-2 xl:grid-cols-4 gap-8">
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
                            :class="backgroundReviewColour(key, stat.value)"
                        >
                            <component
                                :is="stat.icon"
                                class="h-6 w-6 text-white"
                            />
                        </div>
                        <p class="ml-16 font-medium text-zinc-500 capitalize">
                           {{ key }}
                        </p>
                    </dt>
                    <dd class="ml-16 flex items-baseline">
                        <p
                            class="text-2xl font-semibold"
                            :class="textReviewColour(key, stat.value)"
                        >
                            {{ stat.value }}
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
    EmojiHappyIcon,
    AcademicCapIcon,
    LightningBoltIcon,
    ChartSquareBarIcon,
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
        EmojiHappyIcon,
        AcademicCapIcon,
        LightningBoltIcon,
        ChartSquareBarIcon,
        ChatIcon,
    },
    data() {
        return {
            stats: {
                friendliness: {
                    icon: "EmojiHappyIcon",
                    value: 0,
                },
                knowledge: {
                    icon: "AcademicCapIcon",
                    value: 0,
                },
                responsiveness: {
                    icon: "LightningBoltIcon",
                    value: 0,
                },
                overall: {
                    icon: "ChartSquareBarIcon",
                    value: 0,
                },
            },
        };
    },
    mounted() {
        // Simulate API call
        this.fetchStats();
    },
    methods: {
        fetchStats() {
            axios
                .get(`/api/v2/user/${user().id}/chat-reviews/stats`)
                .then((response) => {
                    if (response.data) {
                        const keys = Object.keys(response.data);
                        keys.forEach((key) => {
                            this.stats[key].value = response.data[key];
                        });
                    }
                })
                .catch((error) => {
                    console.error(error);
                });
        },
        textReviewColour(key, value) {
            if (value === 5) {
                return "text-primary-600 dark:text-primary-400";
            } else if (value >= 4 && value < 5) {
                return "text-green-600 dark:text-green-400";
            } else if (value >= 3 && value < 4) {
                return "text-yellow-600 dark:text-yellow-400";
            } else if (value >= 2 && value < 3) {
                return "text-orange-600 dark:text-orange-400";
            } else if (value >= 1 && value < 2) {
                return "text-red-600 dark:text-red-400";
            } else {
                return "text-zinc-600 dark:text-zinc-400";
            }
        },

        backgroundReviewColour(key, value) {
            if (value === 5) {
                return "bg-primary-600 dark:bg-primary-400";
            } else if (value >= 4 && value < 5) {
                return "bg-green-600 dark:bg-green-400";
            } else if (value >= 3 && value < 4) {
                return "bg-yellow-600 dark:bg-yellow-400";
            } else if (value >= 2 && value < 3) {
                return "bg-orange-600 dark:bg-orange-400";
            } else if (value >= 1 && value < 2) {
                return "bg-red-600 dark:bg-red-400";
            } else {
                return "bg-zinc-600 dark:bg-zinc-400";
            }
        },
    },
};
</script>
