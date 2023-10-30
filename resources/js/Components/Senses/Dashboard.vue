<template>
    <div class="w-full flex flex-wrap">
        <div v-for="server in servers">
            <ServerCard :data="server" :client="server.slug">{{ server.title }}</ServerCard>
        </div>
    </div>
</template>

<script>
// Imports
import ServerCard from './ServerCard.vue';
import axios from 'axios';

export default {
    components: {
        ServerCard
    },

    props: {

    },

    mounted() {
        this.load()
    },

    data() {
        return {
            servers: [],
        };
    },

    methods: {
        load() {
            axios.get('api/v2/servers?format=all').then((response) => {
                this.servers = response.data;
            })
        }
    },
};
</script>
