<template>
    <div class="flex flex-col space-y-6">
        <h1 class="text-black text-xl">Are you sure you want to delete {{ this.data.name }}'s chat?</h1>
        <ButtonGroup>
            <SecondaryButton @click="$modals.pop()">Go Back</SecondaryButton>
            <DangerButton @click="deleteChat">Delete</DangerButton>
        </ButtonGroup>
    </div>
</template>
<script>
import axios from 'axios';
import ButtonGroup from '../../Ui/Buttons/ButtonGroup.vue';
import SecondaryButton from '../../Ui/Buttons/SecondaryButton.vue';
import DangerButton from '../../Ui/Buttons/DangerButton.vue';

import EventHub from '../../../Support/EventHub';

export default {
    components: {
        ButtonGroup,
        SecondaryButton,
        DangerButton
    },
    props: {
        data: {
            type: Object,
            default: () => {},
        }
    },
    methods: {
        deleteChat() {
            axios.delete(`/api/v2/chats/${this.data.chat_id}`)
                .then(response => {
                    EventHub.emit('chats:delete', this.data.chat_id);
                    this.$modals.pop();
                });
        }
    },
}
</script>