<template>
    <SecondaryButton @click="fetchChats">Refresh</SecondaryButton>
</template>
<script>
import SecondaryButton from '../../Ui/Buttons/SecondaryButton.vue';

import EventHub from '../../../Support/EventHub';

export default {
    components: {
        SecondaryButton
    },

    methods: {
        fetchChats() {
            EventHub.emit('chats:fetch');
        }
    }
}
</script>