<template>
    <AsideLayout v-bind="$props" :loading="state.is(AsideState.LOADING)" flush>
        <template #title>Hidden User {{ data.id }}</template>
        <template #actions>
            <ButtonGroup v-if="state.not(AsideState.LOADING) && hiddenUser">
                <SecondaryButton @click="unhide">
                    Unhide
                    <template v-if="loading" #icon>
                        <LoadingIcon class="w-5 h-5 text-primary"/>
                    </template>
                </SecondaryButton>
            </ButtonGroup>
        </template>

        <div v-if="state.not(AsideState.LOADING) && hiddenUser">
            <div class="space-y-5 p-5">
                <div v-if="hiddenUser.full_name">
                    <StrongText class="block mb-1">Full Name</StrongText>
                    <Text>{{ hiddenUser.full_name }}</Text>
                </div>
                <div v-if="hiddenUser.email">
                    <StrongText class="block mb-1">Email</StrongText>
                    <Text>{{ hiddenUser.email }}</Text>
                </div>
                <div v-if="hiddenUser.mobile">
                    <StrongText class="block mb-1">Mobile</StrongText>
                    <Text>{{ hiddenUser.mobile }}</Text>
                </div>
                <div v-if="hiddenUser.user_type">
                    <StrongText class="block mb-1">User Type</StrongText>
                    <Text>{{ hiddenUser.user_type.title }}</Text>
                </div>
                <div v-if="hiddenUser.hidden_at">
                    <StrongText class="block mb-1">Hidden At</StrongText>
                    <Text>{{ hiddenUser.hidden_at }}</Text>
                </div>
            </div>
        </div>
    </AsideLayout>
</template>

<script>
import LoadingIcon from '../../Ui/LoadingIcon.vue';
import SecondaryButton from '../../Ui/Buttons/SecondaryButton.vue';
import Text from '../../Ui/Text/Text.vue';
import StrongText from '../../Ui/Text/StrongText.vue';
import DeleteButton from '../../Ui/Buttons/DeleteButton.vue';
import EditButton from '../../Ui/Buttons/EditButton.vue';
import ButtonGroup from '../../Ui/Buttons/ButtonGroup.vue';
import AsideLayout from '../../Layout/AsideLayout.vue';

import formatDateTime from '../../../Filters/FormatDateTime';
import AsideState from '../../../States/AsideState';
import axios from 'axios';
import Aside from '../../../Mixins/Aside';
import eventHub from '../../../Support/EventHub.js';

export default {
    components: {
        AsideLayout,
        ButtonGroup,
        EditButton,
        DeleteButton,
        StrongText,
        Text,
        SecondaryButton,
        LoadingIcon,

    },

    mixins: [Aside],

    data() {
        return {
            state: new AsideState(),
            user: window.user,
            AsideState,

            hiddenUser: null,
            loading: false,
        };
    },
    mounted() {
        this.state.set(AsideState.LOADING);
        this.load();
    },

    methods: {
        formatDateTime,

        load() {
            this.loading = true;
            axios.get("/api/v2/users/" + this.data.id).then((response) => {
                this.hiddenUser = response.data;
                this.state.set(AsideState.IDLE);
                this.loading = false;
            }).catch((error) => {
                this.error = error;
                this.state.set(AsideState.ERROR);
                this.loading = false;
            });
        },

        unhide() {
            this.loading = true;
            axios.post("/api/v2/users/" + this.data.id + "/unhide", { user_id: this.data.id }).then((response) => {
                this.state.set(AsideState.IDLE);
                this.$asides.pop();

                this.$notifications.push({
                    title: 'User Unhidden',
                    description: response.data.full_name + ' has been unhidden.',
                    type: 'info',
                });

                eventHub.emit('user-updated', response.data);
                this.loading = false;
            }).catch((error) => {
                this.error = error;
                this.state.set(AsideState.ERROR);
                this.$asides.pop();
                this.loading = false;
            });
        },

        rowClick(row, type) {

        },
    },
};
</script>
