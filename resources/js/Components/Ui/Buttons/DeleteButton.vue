<template>
    <component :is="!enabled && !noTooltip ? 'Tooltip' : 'div'">
        <slot name="button" :delete-model="deleteModel" :disabled="!enabled">
            <component :is="component ?? 'DangerButton'" :disabled="!enabled" @click="deleteModel">
                <template v-if="$slots.icon" #icon>
                    <slot name="icon" class="my-auto h-5 w-5"></slot>
                </template>
                Delete
            </component>
        </slot>
        <template v-if="!enabled" #content>
            <div v-if="disableType === 'locked'">
                This {{ singularModel }} is locked
            </div>
            <div v-else-if="disableType === 'denied'">Permission denied</div>
            <div v-else-if="disabledMessage">{{ disabledMessage }}</div>
        </template>
    </component>
</template>

<script>
// @todo move to Senses/

import axios from 'axios';
import DangerButton from './DangerButton.vue';
import SecondaryButton from './SecondaryButton.vue';
import Tooltip from '../Tooltip.vue';
import EventHub from '../../../Support/EventHub';

export default {
    components: {
        DangerButton,
        SecondaryButton,
        Tooltip,
    },
    props: {
        id: {
            type: Number,
            required: true,
        },
        singularModel: {
            type: String,
            required: true,
        },
        component: {
            type: String,
            default: 'DangerButton',
            validator(value) {
                return ['DangerButton', 'SecondaryButton'].includes(
                    value
                );
            },
        },
        pluralModel: {
            type: String,
            required: true,
        },
        question: {
            type: String,
            default: 'Are you sure?',
        },
        message: {
            type: String,
            default: null,
        },
        disabledMessage: {
            type: String,
            default: null,
        },
        redirect: {
            type: Boolean,
            default: true,
        },
        closeAsides: {
            type: Boolean,
            default: true,
        },
        data: {
            type: Object,
            default: () => ({}),
        },
        noTooltip: {
            type: Boolean,
            default: false,
        },
        emitUpdated: {
            type: Boolean,
            default: true,
        },
        disabled: {
            type: Boolean,
            default: false,
        },

        url:{
            type:String,
            default:null
        },

        errorStayOpen:{
            type:Boolean,
            default:false
        }
    },

    emits: ['error'],

    data() {
        return {
            user: window.user,
            modal: false,
            disableType: null,
        };
    },


    computed: {
        deleteUrl() {
            if(this.url) {
                return this.url;
            }
            return `/api/v2/${this.pluralModel}/${this.id}`;
        },
        proxyMessage() {
            if (this.message) {
                return this.message;
            }
            return `You won't be able to access this ${this.singularModel.replaceAll(
                '-',
                ' '
            )} again.`;
        },
        enabled() {
            if (this.disabled) {
                return false;
            }

            if (this.data?.locked_at !== null) {
                this.disableType = 'locked';
                return false;
            }

            if (
                this.singularModel !== 'user' &&
                !this.user().can(`delete-${this.singularModel}`) &&
                !(
                    this.data.owner_ids &&
                    this.user().can(`delete-own-${this.singularModel}`) &&
                    this.data.owner_ids.includes(this.user().id)
                ) &&
                !(
                    this.user().managed_user_ids &&
                    this.user().can(`delete-managed-${this.singularModel}`) &&
                    this.user().managed_user_ids.some((r) =>
                        this.data.owner_ids.includes(r)
                    )
                )
            ) {
                console.log(
                    this.user().can(`delete-${this.singularModel}`) ?
                    'true' :
                    'false'
                );
                console.log(this.user().id);
                console.log(this.singularModel);
                this.disableType = 'denied';
                return false;
            }

            if (
                this.deleteModel === 'user' &&
                !this.user().can(`delete-user`) &&
                !(
                    this.user().can(`delete-own-user`) &&
                    this.data.id === this.user().id
                ) &&
                !(
                    this.user().managed_user_ids &&
                    this.user().can(`delete-managed-user`) &&
                    this.user().managed_user_ids.includes(this.data.id)
                )
            ) {
                this.disableType = 'denied';
                return false;
            }

            this.disableType = null;
            return true;
        },
    },
    methods: {
        async deleteModel() {
            const confirmed = await this.$dialogs.danger(
                this.question,
                this.proxyMessage
            );

            if (confirmed) {
                axios
                    .delete(this.deleteUrl)
                    .then(() => {
                        EventHub.emit(`${this.singularModel}-deleted`, this.id);
                        EventHub.emit(`${this.singularModel}-updated`);

                        if (this.closeAsides) {
                            this.$asides.pop();
                        }

                        if (this.redirect) {
                            window.location = `/${this.pluralModel}`;
                        }
                    })
                    .catch((error) => {
                        this.$emit('error', error?.response?.data ?? error, error);
                        // if (this.redirect) {
                        if(!this.errorStayOpen) {
                            this.$asides.pop();
                        }
                        this.$notifications.push({
                            title: 'Delete failed',
                            description: error.response.data.message,
                            postText: error.response.data?.uuid ?
                                `UUID: ${error.response.data.uuid}` :
                                null,
                            type: 'danger',
                        });
                        // }
                    });
            }
        },
    },
};
</script>
