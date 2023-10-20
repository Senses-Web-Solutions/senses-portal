<template>
    <InputGroup
        :label="label"
        :id="id"
        :is-valid="isValid"
        v-if="type == 'json'"
    >
        <SeTextArea
            :id="id"
            ref="textarea"
            v-model="stringData"
            :name="name"
            class="block w-full rounded-md border-zinc-300 shadow-sm focus:border-purple-500 focus:ring-4 focus:ring-purple-200 font-mono"
            :placeholder="placeholder"
            :class="{ 'border-red-500': !isValid }"
            :rows="rowLength"
            :full-height="fullHeight"
            @keydown.tab.prevent
            @update:model-value="emitData($event)"
        />
    </InputGroup>

    <InputGroup :id="id" :label="label" :is-valid="true" v-else>
        <span
            v-for="(row, index) in arrayData"
            :key="index"
            class="grid grid-cols-4 gap-x-4 pb-3"
        >
            <div class="col-span-4 pb-3">
                <SeLabel class="capitalize leading-5">
                    {{ index.replaceAll('_', ' ').replaceAll('-', ' ') }}
                </SeLabel>
            </div>
            <div class="col-span-4">
                <div v-if="Array.isArray(row)" class="space-y-2">
                    <SeInput
                        v-for="(field, fieldIndex) in row"
                        :key="fieldIndex"
                        :id="index + '_' + fieldIndex"
                        :name="index + '_' + fieldIndex"
                        v-model="arrayData[index][fieldIndex]"
                        @update:model-value="
                            emitRowData($event, index, fieldIndex)
                        "
                    />
                </div>
                <div v-else>
                    <SeInput
                        class="mb-2"
                        :name="index"
                        :id="index"
                        v-model="arrayData[index]"
                        @update:model-value="emitRowData($event, index, null)"
                    />
                </div>
            </div>
        </span>
    </InputGroup>

    <CollapseTransition>
        <p class="mt-4 text-danger-600" v-if="proxyError && !error">
            The data must be a valid JSON string.
        </p>
    </CollapseTransition>
</template>
<script>
import SeLabel from './SeLabel.vue';
import SeInput from './SeInput.vue';
import SeTextArea from './SeTextArea.vue';
import InputGroup from './InputGroup.vue';
import CollapseTransition from '../Transitions/CollapseTransition.vue';

export default {
    components: {
        SeLabel,
        SeInput,
        CollapseTransition,
        SeTextArea,
        InputGroup,
    },
    emits: ['update:modelValue', 'invalidJson'],
    props: {
        type: {
            type: String,
            default: 'json',
            validator(value) {
                return ['default', 'json'].includes(value);
            },
        },
        label: {
            type: String,
            required: false,
        },
        modelValue: {
            default: null,
        },
        error: {
            required: false,
            default: null,
        },
        placeholder: {
            //todo curently defined as props, could be v-bind="$attrs" to pass them all onto the input?
            type: String,
            default: null,
        },
        name: {
            type: String,
            required: true,
        },
        id: {
            type: String,
            required: true,
        },
        fullHeight: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            stringData: {},
            arrayData: {},
            proxyError: null,
        };
    },
    computed: {
        isValid() {
            if (this.error && this.error.errors) {
                return !this.error.errors[this.name];
            }
            if (this.proxyError && this.proxyError.errors) {
                return !this.proxyError.errors['json'];
            }
            return true;
        },
        rowLength() {
            var rows = 2;
            if (this.modelValue) {
                var data = this.modelValue;
                Object.values(data).forEach((value) => {
                    if (value && typeof value == 'object') {
                        rows += value.length + 2;
                    } else {
                        rows += 1;
                    }
                });
            }

            return rows;
        },
    },

    mounted() {

        this.stringData = JSON.stringify(this.modelValue, null, '\t');
        this.arrayData = this.modelValue;
    },

    watch: {
        modelValue: {
            deep: true,
            handler(newValue) {
                this.stringData = JSON.stringify(newValue, null, '\t');
                this.arrayData = newValue;
            },
        },
    },

    methods: {
        focus() {
            this.$refs.input.focus();
        },
        emitData(data) {
            try {
                let request = JSON.parse(data);
                this.$emit('update:modelValue', request);
                this.proxyError = null;
                this.$emit('invalidJson', false);
            } catch (e) {
                this.invalidJson();
            }
        },
        emitRowData(data, index, fieldIndex) {
            try {
                var proxyData = this.arrayData;
                if (fieldIndex) {
                    proxyData[index][fieldIndex] = String(data);
                } else {
                    proxyData[index] = String(data);
                }
                let request = JSON.parse(JSON.stringify(proxyData));
                this.$emit('update:modelValue', proxyData);
                this.proxyError = null;
                this.$emit('invalidJson', false);
            } catch (e) {
                console.log(e);
                this.invalidJson();
            }
        },
        invalidJson() {
            this.proxyError = {
                message: 'Your JSON is invalid',
            };
            this.$emit('invalidJson', true);
        },
    },
};
</script>
