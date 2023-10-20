<template>
    <component :is="!enabled ? 'Tooltip' : 'div'">
        <slot name="button" v-bind="{ editModel: () => form ? $asides.push(form, formData) : $emit('click', $event), disabled: !enabled }">
            <SecondaryButton :disabled="!enabled" @click="buttonClicked">
                Edit
            </SecondaryButton>
        </slot>

        <template #content>
            <div v-if="disableType === 'locked'">
                This {{ model.replaceAll('-', ' ') }} is locked
            </div>
            <div v-else-if="disableType === 'denied'">Permission denied</div>
            <div v-else-if="message">{{ message }}</div>
        </template>
    </component>
</template>

<script>
// @todo move to Senses/

import SecondaryButton from './SecondaryButton.vue';
import Tooltip from '../Tooltip.vue';

export default {
    components: {
        Tooltip,
        SecondaryButton
    },
    props: {
        form: {
            type: String,
            default: null,
        },
        link: {
            type: String,
            default: null,
        },
        id: {
            required: true,
        },
        model: {
            type: String,
            default: null,
        },
        data: {
            type: Object,
            default: () => ({}),
        },
        asideData: {
            type: Object,
            default: () => ({}),
        },
        disabled: {
            type: Boolean,
            default: false,
        },
        message: {
            type: String,
            default: '',
        },
    },
    emits: ['click'],
    data() {
        return {
            user: window.user,
            disableType: null,
        };
    },
    methods: {
        buttonClicked(event) {
            if (this.form) {
                this.$asides.push(this.form, this.formData);
            } else if (this.link) {
                window.location.href = this.link;
            } else {
                this.$emit('click', event);
            }
        },
    },
    computed: {
        formData() {
            return {
                ...this.asideData,
                ...{
                    id: parseInt(this.id)
                }
            };
        },
        enabled() {
            if (this.disabled) {
                return false;
            }

            if (this.data?.locked_at !== null) {
                this.disableType = 'locked';
                return false;
            } else if (
                this.model !== 'user' &&
                !this.user().can(`update-${this.model}`) &&
                !(
                    this.data.owner_ids &&
                    this.user().can(`update-own-${this.model}`) &&
                    this.data.owner_ids.includes(this.user().id)
                ) &&
                !(
                    this.user().managed_user_ids &&
                    this.user().can(`update-managed-${this.model}`) &&
                    this.user().managed_user_ids.some((r) =>
                        this.data.owner_ids.includes(r)
                    )
                )
            ) {
                this.disableType = 'denied';
                return false;
            }
            this.disableType = null;
            return true;
        },
    },
};
</script>
