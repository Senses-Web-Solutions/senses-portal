<template>
    <div class="w-full flex flex-wrap p-2">
        <div class="p-2" v-for="server in servers">
            <ServerCard :data="server" />
        </div>
    </div>
</template>

<script>
import SmallText from '../Ui/Text/SmallText.vue';
// Imports
import ServerList from "./Servers/ServerListRow.vue";
import ServerCard from "./Servers/ServerCard.vue";
import axios from "axios";

export default {
    components: {
        ServerList,
        ServerCard,
        SmallText,
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
            axios.get("api/v2/servers?format=all&sort=title").then((response) => {
                // this.servers = response.data.filter(server => server['title'] != 'Senses Portal');
                this.servers = response.data;
            });
        },
    },
};
</script>
