<template>
    <div class="space-y-4">
        <div class="pr-8 text-black">
            To initialise your server, SSH in and run this command at the root
        </div>
        <div class="select-all bg-zinc-100 text-black p-4 rounded-lg flex items-center">
            {{ 'wget -q --show-progress http://dev.portal.senses.co.uk/scripts/install.sh && sudo bash install.sh --key=' + data.token }}
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 ml-4 text-zinc-700 rotate-90 cursor-pointer hover:text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
            </svg>
        </div>
    </div>
</template>

<script>
// Imports

export default {
    components: {

    },

    props: {
        data: {
            type: Object
        }
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
