<template>
    <AsideLayout flush size="sm">
        <template #title>Chat Site {{ data.title }}</template>
        <template #actions>
            <ButtonGroup v-if="state.not(AsideState.LOADING) && chatSite">
                <EditButton form="AllowedChatSiteForm" model="allowed-chat-site" :id="data.id" :data="chatSite" />
                <DeleteButton :id="data.id" :redirect="false" singular-model="allowed-chat-site" plural-model="allowed-chat-sites" :data="chatSite" />
            </ButtonGroup>
        </template>
        <div class="divide-y divide-gray-200 border-b border-gray-200">
            <div v-if="chatSite" class="space-y-5 p-4 sm:p-6">
                <div class="space-y-5 p-4">
                    <div v-if="chatSite.title">
                        <StrongText class="mb-1 block">Title</StrongText>
                        <Text>{{ chatSite.title }}</Text>
                    </div>
                    <div v-if="chatSite.url">
                        <StrongText class="mb-1 block">Url</StrongText>
                        <Text>{{ chatSite.url }}</Text>
                    </div>
                </div>
            </div>
        </div>
    </AsideLayout>
</template>

<script>
import axios from 'axios';
import AsideLayout from '../../Layout/AsideLayout.vue';
import StrongText from '../../Ui/Text/StrongText.vue';
import Text from '../../Ui/Text/Text.vue';
import Aside from '../../../Mixins/Aside';
import Tag from "../../Ui/Tags/Tag.vue";
import ButtonGroup from '../../Ui/Buttons/ButtonGroup.vue';
import DeleteButton from '../../Ui/Buttons/DeleteButton.vue';
import EditButton from '../../Ui/Buttons/EditButton.vue';

import AsideState from '../../../States/AsideState';
import formatDateTime from '../../../Filters/FormatDateTime';
import eventHub from '../../../Support/EventHub';
import { capitalize } from 'lodash-es';
import { getBackendClientConfig } from '../../../Support/client';

export default {
    components: {
        AsideLayout,
        StrongText,
        Text,
        Tag,
        DeleteButton,
        EditButton,
        ButtonGroup
    },
    mixins: [Aside],
    data() {
        return {
            user: window.user,
            error: null,
            chatSite: {},
            state: new AsideState(),
            AsideState,
        };
    },
    mounted() {
        this.load();
        eventHub.on('allowed-chat-site-updated', () => {
            this.$asides.pop();
        });
    },
    methods: {
        capitalize,
        getBackendClientConfig,
        formatDateTime,
        load() {
            this.state.set(AsideState.LOADING);
            axios.get(`/api/v2/allowed-chat-sites/${this.data.id}`).then((response) => {
                this.chatSite = response.data;
                this.state.set(AsideState.IDLE);
            });
        },
        formSuccess(data) {
            eventHub.emit('allowed-chat-site-updated', data);
            if (this.data.id) {
                this.$asides.pop();
            }
        },
        formFailure(error) {
            this.state.set(AsideState.ERROR);
            this.error = error.response.data;
        },
    },
};
</script>
