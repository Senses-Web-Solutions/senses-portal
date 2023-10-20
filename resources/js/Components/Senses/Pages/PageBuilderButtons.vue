<template>
    <div class="space-x-4">
        <SecondaryButton @click="toggleSettings">Settings</SecondaryButton>

        <PrimaryButton
        @click="save"
        :disabled="(!user().can('create-page') && !user().can('update-page')) || saveLoading"
    >
        Save
        <template
            v-if="saveLoading"
            #icon
        >
            <LoadingIcon />
        </template>
    </PrimaryButton>
    </div>
    
</template>
<script>
import TransitionTimings from '../../../Enums/TransitionTimings';
import PrimaryButton from '../../Ui/Buttons/PrimaryButton.vue';
import SecondaryButton from '../../Ui/Buttons/SecondaryButton.vue';
import EventHub from '../../../Support/EventHub';
import LoadingIcon from '../../Ui/LoadingIcon.vue';


export default {
    components: {
        LoadingIcon,
        PrimaryButton,
        SecondaryButton,
    },
    data() {
        return {
            user: window.user,
            TransitionTimings,
            saveLoading: false,
        };
    },
    mounted() {
        EventHub.on('page-saving', () => {
            this.saveLoading = true;
        });
        EventHub.on('page-save-complete', () => {
            this.saveLoading = false;
        });
        EventHub.on('page-save-error', () => {
            this.saveLoading = false;
        });
    },
    methods: {
        save() {
            EventHub.emit('page-save');
        },

        toggleSettings() {
            console.log('settings');
            EventHub.emit('page-save');
        }
    },
};
</script>
