<template>
    <div class="w-full flex flex-wrap">
        <div v-for="server in servers">
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
