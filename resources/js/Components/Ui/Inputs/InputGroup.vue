<template>
    <div :class="{
            'space-y-1.5': layout == 'vertical' && slotEmpty($slots.buttons),
            'space-y-2': layout == 'vertical' && !slotEmpty($slots.buttons),
            'flex items-center space-x-4': layout == 'horizontal',
        }">
        <div class="flex items-center justify-between" v-if="label">
            <SeLabel :for="id" :required="required">
                {{ label }}
            </SeLabel>
            <slot name="buttons"></slot>
        </div>
        <div class="relative leading-none" :class="'cy_' + id">
            <div :class="{ flex: $slots.icon || flexInput }">
                <span v-if="$slots.icon" class="inline-flex items-center rounded-l-md border border-r-0 border-zinc-300 bg-zinc-50 px-3 text-zinc-500 shadow-sm sm:text-sm">
                <slot name="icon"></slot>
                </span>
                <slot></slot>
            </div>
            <div v-if="!isValid" class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3" :class="{
                    'right-0': !insetValidationIcon,
                    'right-6': insetValidationIcon,
                }">
                <ExclamationCircleIcon class="h-5 w-5 text-danger-500" aria-hidden="true" v-if="showErrorIcon" />
            </div>
        </div>

        <p v-if="helpText" class="text-sm text-zinc-500">
            {{ helpText }}
        </p>
    </div>
</template>

<script>
import {
    ExclamationCircleIcon
} from '@heroicons/vue/solid';
import SeLabel from './SeLabel.vue';
import slotEmpty from '../../../Support/slotEmpty';

export default {
    components: {
        SeLabel,
        ExclamationCircleIcon,
    },
    props: {
        layout: {
            type: String,
            default: 'vertical',
        },
        label: {
            type: String,
            default: null,
        },
        id: {
            type: String,
            required: true,
        },
        // TODO remove isValid, replace with error
        isValid: {
            type: Boolean,
            default: true,
        },
        error: {
            type: Object,
            default: () => ({}),
        },
        required: {
            type: Boolean,
            default: false,
        },
        helpText: {
            type: [String, null],
            default: null,
        },
        insetValidationIcon: {
            type: Boolean,
            default: false,
        },
        flexInput: {
            type: Boolean,
            default: false,
        },
        showErrorIcon: {
            type: Boolean,
            default: true,
        },
    },
    methods: {
        slotEmpty,
    },
};
</script>
