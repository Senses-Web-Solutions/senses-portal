<template>
    <div class="w-full flex flex-wrap">
        <ServerCard client="ietg">IETG</ServerCard>
        <ServerCard client="lanes">Lanes</ServerCard>
        <ServerCard client="iwjs">IWJS</ServerCard>
    </div>
</template>

<script>
import ServerCard from './ServerCard.vue';
// Imports

export default {
    components: {
        ServerCard
    },

    props: {

    },

    mounted() {

    },

    data() {
        return {

        };
    },

    methods: {

    },
};
</script>
