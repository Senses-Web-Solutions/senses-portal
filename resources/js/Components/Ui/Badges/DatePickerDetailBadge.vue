<template>
    <div>
        <DetailBadge
            clickable
            has-arrow
            colour="gray-light"
            :loading="state.is(FormState.SUBMITTING)"
            :no-floating-label="noFloatingLabel"
            @badge-click="openFlatpickr"
        >
            <template #title>
                <slot />
            </template>
            {{ modelValue ? format(parse(modelValue, Format.DATE, new Date()), displayFormat) : 'Select a date' }}
        </DetailBadge>
        <input ref="input" type="hidden" @input="change($event.target.value)" />
    </div>
</template>
<script>
import flatpickr from 'flatpickr';
import { format, parse } from 'date-fns';
import { ref, reactive, onMounted } from 'vue';
import DetailBadge from './DetailBadge.vue';
import FormState from '../../../States/FormState';
import Format from '../../../Enums/Format';

export default {
    components: {
        DetailBadge,
    },
    props: {
        modelValue: {
            type: [String, null],
            required: true,
        },
        displayFormat: {
            type: String,
            required: false,
            default: Format.DATE
        },
        noFloatingLabel: {
            type: Boolean,
            default: false
        }
    },
    emits: ['update:modelValue'],
    setup (props, { emit }) {
        const state = reactive(new FormState());

        const button = ref(null);

        function change (value) {
            emit('update:modelValue', value);
        }

        const input = ref(null);

        const fp = ref(null);

        onMounted(() => {
            fp.value = flatpickr(input.value, {
                defaultDate: props.modelValue
            });
            // I don't want to hear it.
            fp.value.element.classList.add('w-0', 'h-0', 'overflow-hidden', '!border-none', '!p-0', 'opacity-0', 'absolute');
        });

        function openFlatpickr () {
            fp.value.element.click();
        }

        return {
            button,
            FormState,
            state,
            change,
            input,
            openFlatpickr,
            format,
            parse,
            Format
        };
    },
};
</script>
