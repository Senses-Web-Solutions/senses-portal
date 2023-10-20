<template>
    <EditButton form="UserForm" model="user" :data="data" :id="data.id"/>
    <UserButtonsMenu :data="data"></UserButtonsMenu>
</template>
<script>

import TransitionTimings from '../../../Enums/TransitionTimings';
import EditButton from '../../Ui/Buttons/EditButton.vue';
import UserButtonsMenu from './UserButtonsMenu.vue';

export default {
    components: {
        EditButton,
        UserButtonsMenu,
    },
    props: {
        data: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            user: window.user,
            TransitionTimings
        };
    },
    methods: {
    },
};
</script>
