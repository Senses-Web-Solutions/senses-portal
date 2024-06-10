<template>
    <ButtonGroup>
        <AddButton form="AllowedChatSiteForm" model="allowed-chat-site" text="Add Site" />
    </ButtonGroup>
</template>
<script>
import ButtonGroup from "../../Ui/Buttons/ButtonGroup.vue";
import AddButton from "../../Ui/Buttons/AddButton.vue";

export default {
    components: {
        ButtonGroup,
        AddButton
    }
}
</script>