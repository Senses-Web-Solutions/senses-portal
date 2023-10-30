<template>
    <div class="space-y-16 xl:space-y-20">
        <!-- Recent activity table -->
        <div class="overflow-hidden border-zinc-200">
            <div class="lg:mx-0 lg:max-w-none">
                <table class="w-full table-fixed divide-zinc-100 text-left">
                    <tbody>
                        <tr class="text-md text-zinc-500">
                            <th
                                class="relative isolate border-b border-zinc-200 bg-zinc-50 py-2 pl-6 font-normal"
                            >
                                <span
                                    class="inline-flex gap-x-1.5 py-0 align-middle items-center"
                                >
                                    Title
                                    <svg
                                        class="mt-px h-4 w-4 fill-zinc-500 stroke-none"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                        aria-hidden="true"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M14.77 12.79a.75.75 0 01-1.06-.02L10 8.832 6.29 12.77a.75.75 0 11-1.08-1.04l4.25-4.5a.75.75 0 011.08 0l4.25 4.5a.75.75 0 01-.02 1.06z"
                                            clip-rule="evenodd"
                                        ></path>
                                    </svg>
                                </span>
                            </th>

                            <th
                                class="relative isolate w-36 border-b border-zinc-200 bg-zinc-50 py-2 font-normal"
                            >
                                <span
                                    class="-ml-6 inline-flex gap-x-1.5 py-0 align-middle items-center"
                                >
                                    Load: 1 · 5 · 15
                                    <svg
                                        class="mt-px h-4 w-4 fill-zinc-500 stroke-none"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                        aria-hidden="true"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                            clip-rule="evenodd"
                                        ></path>
                                    </svg>
                                </span>
                            </th>

                            <th
                                class="relative isolate w-36 border-b border-zinc-200 bg-zinc-50 py-2 font-normal"
                            >
                                <span
                                    class="inline-flex gap-x-1.5 py-0 align-middle items-center"
                                >
                                    Trend
                                    <svg
                                        class="mt-px h-4 w-4 fill-zinc-500 stroke-none"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                        aria-hidden="true"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                            clip-rule="evenodd"
                                        ></path>
                                    </svg>
                                </span>
                            </th>

                            <th
                                class="relative isolate w-36 border-b border-zinc-200 bg-zinc-50 py-2 font-normal"
                            >
                                Space
                            </th>
                            <th
                                class="relative isolate w-36 border-b border-zinc-200 bg-zinc-50 py-2 font-normal"
                            >
                                Volume
                            </th>
                            <th
                                class="relative isolate w-36 border-b border-zinc-200 bg-zinc-50 py-2 font-normal"
                            >
                                RAM
                            </th>
                            <th
                                class="relative isolate w-36 border-b border-zinc-200 bg-zinc-50 py-2 pr-6 font-normal"
                            >
                                Swap
                            </th>
                            <th
                                class="relative isolate w-36 border-b border-zinc-200 bg-zinc-50 py-2 pr-6 font-normal"
                            >
                                Data
                            </th>
                        </tr>

                        <template v-for="server in servers">
                            <ServerList :data="server" :client="server.slug"></ServerList>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="w-full flex flex-wrap">
        <div v-for="server in servers">
            <ServerCard :data="server" :client="server.slug">{{server.title}}</ServerCard>
        </div>
    </div>
</template>

<script>
// Imports
import ServerList from "./ServerList.vue";
import ServerCard from "./ServerCard.vue";
import axios from "axios";

export default {
    components: {
        ServerList,
        ServerCard,
    },

    props: {},

    mounted() {
        this.load();
    },

    data() {
        return {
            servers: [],
        };
    },

    methods: {
        load() {
            axios.get("api/v2/servers?format=all").then((response) => {
                this.servers = response.data;
            });
        },
    },
};
</script>
