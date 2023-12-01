<template>
    <div class="w-full grid 4K:grid-cols-12 1440:grid-cols-8 desktop:grid-cols-6 grid-cols-4">
        <div class="p-2 mx-auto" v-for="server in servers">
            <ServerCard :data="server" />
        </div>
    </div>
</template>

<script>
// Imports
import ServerList from "./Servers/ServerListRow.vue";
import ServerCard from "./Servers/ServerCard.vue";
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
