<template>
    <div class="flex flex-col space-y-6">
        <h1 class="text-black text-xl">Are you sure you want to mark this chat as completed?</h1>
        <ButtonGroup>
            <SecondaryButton @click="$modals.pop()">Go Back</SecondaryButton>
            <SuccessButton @click="completeChat">Complete</SuccessButton>
        </ButtonGroup>
    </div>
</template>
<script>
import axios from 'axios';
import ButtonGroup from '../../Ui/Buttons/ButtonGroup.vue';
import SecondaryButton from '../../Ui/Buttons/SecondaryButton.vue';
import SuccessButton from '../../Ui/Buttons/SuccessButton.vue';

import EventHub from '../../../Support/EventHub';

export default {
    components: {
        ButtonGroup,
        SecondaryButton,
        SuccessButton
    },
    props: {
        data: {
            type: Object,
            default: () => {},
        }
    },
    methods: {
        completeChat() {
            axios.get(`/api/v2/resolve/chats/${this.data.chat_id}`)
                .then(response => {
                    EventHub.emit('chats:complete', this.data.chat_id);
                    this.$modals.pop();
                });
        }
    },
}
</script>