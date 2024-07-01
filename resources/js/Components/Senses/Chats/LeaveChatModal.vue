<template>
    <div class="flex flex-col space-y-6">
        <h1 class="text-black text-xl">Are you sure you want to leave {{ data.name }}'s chat?</h1>
        <ButtonGroup>
            <SecondaryButton @click="$modals.pop()">Go Back</SecondaryButton>
            <WarningButton @click="leaveChat">Leave</WarningButton>
        </ButtonGroup>
    </div>
</template>
<script>
import axios from 'axios';
import ButtonGroup from '../../Ui/Buttons/ButtonGroup.vue';
import SecondaryButton from '../../Ui/Buttons/SecondaryButton.vue';
import WarningButton from '../../Ui/Buttons/WarningButton.vue';

export default {
    components: {
        ButtonGroup,
        SecondaryButton,
        WarningButton
    },
    props: {
        data: {
            type: Object,
            default: () => {},
        }
    },
    methods: {
        leaveChat() {
            axios.get(`/api/v2/leave/chats/${this.data.chat_id}`).then(response => {
                this.$modals.pop();
            });
        }
    },
}
</script>