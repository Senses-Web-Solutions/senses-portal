<template>
    <DangerAlert :dismissable="dismissable" @click="showErrorView" v-if="error && error.message">
        <template #title>{{ messages.length > 0 ? 'There was an error with your submission' : error.message.replaceAll('<li>', '').replaceAll('</li>', '') }}</template>
        <div v-if="messages">
            <div class="mt-2 text-red-700">
                <ul class="pl-5 space-y-1 list-disc">
                    <li v-for="(message, index) in messages" :key="index">
                        <span v-html="capitalizeFirstLetter(message)" />
                    </li>
                </ul>
            </div>
        </div>
        <CollapseTransition>
            <div v-if="error.uuid && showUUID" class="text-sm mt-2">Error UUID: {{ error.uuid }}</div>
        </CollapseTransition>
    </DangerAlert>
</template>
<script>
import CollapseTransition from '../Transitions/CollapseTransition.vue';
import DangerAlert from '../Alerts/DangerAlert.vue';
export default {
    components: {
        DangerAlert,
        CollapseTransition,
    },
    props: {
        error: {
            required: false,
        },
        dismissable: {
            default: true
        }
    },
    computed: {
        messages() {
            if (this.error?.errors) {
                var messages = [];
                Object.keys(this.error.errors).forEach(key => {
                    var error = this.error.errors[key];
                    if (!messages.includes(error[0].replace('.', ' '))) {
                        messages.push(error[0].replace('.', ' '));
                    }
                });
                return messages;
            }
            return [];
        }
    },
    data() {
        return {
            user: window.user,
            showUUID: false
        }
    },
    methods: {
        capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        },
        showErrorView() {
            if (this.user().can('show-error')) {
                this.$asides.push('ErrorView', { error: this.error })
            }
        }
    }
};
</script>
