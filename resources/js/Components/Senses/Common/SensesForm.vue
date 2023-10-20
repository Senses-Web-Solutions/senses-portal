<template>
    <div ref="root">
        <Errors :error="proxyError" class="mb-6" :dismissable="true" />
        <InfoAlert v-if="modelValue?.locked_at" class="mb-6">
            <template #icon>
                <LockClosedIcon />
            </template>
            <template #title>{{ modelValue?.object ?? 'This' }} is locked and can't be
                edited.</template>
        </InfoAlert>
        <div v-for="(fieldGroup, fieldGroupIndex) in fields" :key="fieldGroup.title + fieldGroup.fields.length">
            <FormLayout>
                <template #icon>
                    <component :is="fieldGroup.icon" v-if="fieldGroup.icon"></component>
                    <InformationCircleIcon v-else></InformationCircleIcon>
                </template>
                <template #title>{{ fieldGroup.title }}</template>
                <template #description>{{ fieldGroup.description }}</template>
                <FieldGroup
                    v-model="proxyModel"
                    v-model:fields="fieldGroup.fields"
                    :error="proxyError"
                    :field-types="fieldTypes"
                    @permission-denied="permissionDenied = true">
                    <template v-for="field in fieldGroup.fields.filter(
                            (subField) => subField.component === 'template'
                        )" :key="field.name" #[field.name]>
                        <slot :name="field.name"></slot>
                    </template>
                </FieldGroup>
            </FormLayout>
            <Divider v-if="fields.length > 1 && !isLastFieldGroup(fieldGroupIndex)" />
        </div>
        <slot></slot>
        <div>
            <teleport v-if="teleportButtons" :to="'#aside-layout-footer-' + asideIndex">
                <div class="flex border-t items-center border-zinc-200 p-3 sm:p-6" :class="slotEmpty($slots['buttons-left']) ? 'justify-between' : 'justify-end'">
                    <slot name="buttons-left"></slot>
                    <ButtonGroup>
                        <SecondaryButton @click="$asides.pop()">Cancel</SecondaryButton>
                        <slot name="buttons"></slot>
                        <component :is="permissionDenied ? 'Tooltip' : 'div'">
                            <PrimaryButton type="primary" :disabled="saveDisabled" @click="onSubmit ? onSubmit() : submit()">
                                <template v-if="state.is(AsideState.SUBMITTING)" #icon>
                                    <LoadingIcon class="w-5 h-5"/>
                                </template>
                                Save
                            </PrimaryButton>
                            <template v-if="permissionDenied" #content>
                                Permission denied
                            </template>
                        </component>
                    </ButtonGroup>
                </div>
            </teleport>
            <div v-else class="border-t border-zinc-200 pt-6 pb-8 mt-6">
                <div class="flex items-center" :class="slotEmpty($slots['buttons-left']) ? 'justify-between' : 'justify-end'">
                    <slot name="buttons-left"></slot>
                    <ButtonGroup>
                        <SecondaryButton @click="$asides.pop()">Cancel</SecondaryButton>
                        <slot name="buttons"></slot>
                        <component :is="permissionDenied ? 'Tooltip' : 'div'">
                            <PrimaryButton type="primary" :disabled="saveDisabled" @click="onSubmit ? onSubmit() : submit()">
                                <template v-if="state.is(AsideState.SUBMITTING)" #icon>
                                    <LoadingIcon class="w-5 h-5"/>
                                </template>
                                {{ saveText }}
                            </PrimaryButton>
                            <template v-if="permissionDenied" #content>
                                Permission denied
                            </template>
                        </component>
                    </ButtonGroup>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
// TODO: Frontend validation not implemented (but fields can be set to required)
// TODO: Visuals Needed
// TODO: Validation Errors need to appear below each field
// TODO: Events on success
// TODO: Test other field types
import axios from 'axios';
import {
    InformationCircleIcon,
    LockClosedIcon
} from '@heroicons/vue/outline';
import FieldGroup from '../../Ui/FieldGroup.vue';
import SeButton from '../../Ui/Buttons/SeButton.vue';
import SmallText from '../../Ui/Text/SmallText.vue';
import SecondaryButton from '../../Ui/Buttons/SecondaryButton.vue';
import ButtonGroup from '../../Ui/Buttons/ButtonGroup.vue';
import PrimaryButton from '../../Ui/Buttons/PrimaryButton.vue';
import FormLayout from '../../Layout/FormLayout.vue';
import AsideState from '../../../States/AsideState';
import Divider from '../../Ui/Divider.vue';
import Tooltip from '../../Ui/Tooltip.vue';
import Errors from '../../Ui/Errors/Errors.vue';
import InfoAlert from '../../Ui/Alerts/InfoAlert.vue';
import processFormRelationships from '../../../Support/processFormRelationships';
import LoadingIcon from '../../Ui/LoadingIcon.vue';
import slotEmpty from "../../../Support/slotEmpty";

export default {
    components: {
        PrimaryButton,
        SmallText,
        Tooltip,
        ButtonGroup,
        SecondaryButton,
        FieldGroup,
        SeButton,
        FormLayout,
        Divider,
        InformationCircleIcon,
        Errors,
        InfoAlert,
        LockClosedIcon,
        LoadingIcon,
    },
    props: {
        url: {
            type: String,
            required: true,
        },
        fields: {
            type: Array,
            required: true,
        },
        modelValue: {
            type: Object,
            required: true,
        },
        id: {
            type: Number,
            required: false,
        },
        fieldTypes: {
            type: Object,
            default: () => {},
        },
        onSubmit: {
            type: Function,
        },
        asideIndex: {
            type: Number,
            default: -1,
        },
        error: {
            type: Object,
            default: null,
        },
        saveText: {
            type: String,
            default: 'Save',
        },
        disableButtons: {
            type: Boolean,
            default: false,
        },
        saveModal: {
            type: Object,
            default: null,
        },
    },
    emits: [
        'update:modelValue',
        'update:fields',
        'update:error',
        'success',
        'loaded',
        'stateChange',
    ],
    data() {
        return {
            AsideState,
            state: new AsideState(),
            teleportButtons: false,
            permissionDenied: false,
        };
    },
    computed: {
        saveDisabled() {
            const invalidState = this.state.is(AsideState.LOADING) || this.state.is(AsideState.SUBMITTING);
            return (invalidState || (this.modelValue && this.modelValue.locked_at && this.modelValue.locked_at !== null) || this.permissionDenied || this.disableButtons);
        },
        proxyModel:{
            get() {
                return this.modelValue;
            },

            set(val) {
                this.$emit('update:modelValue', val);
            },
        },
        proxyError: {
            get() {
                return this.error;
            },
            set(val) {
                this.$emit('update:error', val);
            },
        },
        method() {
            console.log(this.modelValue);
            return this.modelValue && this.modelValue.id ? 'patch' : 'post';
        },
        formUrl() {
            if (this.method === 'patch') {
                return `${this.url}/${this.modelValue.id}`;
            }
            return this.url;
        },
        templateFields() {
            let fields = [];
            this.fields.forEach((fieldGroup) => {
                fields = fields.concat(
                    fieldGroup.fields.filter(
                        (field) => field.type === 'template'
                    )
                );
            });
            return fields;
        },
    },
    watch: {
        // eslint-disable-next-line func-names
        'state.states': function () {
            this.$emit('stateChange', this.state);
        },
        fields: {
            deep: true,
            handler() {
                this.$nextTick(() => {
                    // The dynamic slots in this component will not work
                    // properly if the fields are updated unless
                    // this force update is here. I lost my sanity
                    // debugging this DO NOT REMOVE.
                    this.$forceUpdate();
                });
            },
        },
    },
    mounted() {
        // Log.info('Mounted');

        // This is to bind states on XYZForm to the state on senses form so that sensesForm's state can be edited from the XYZForm side.
        this.$emit('stateChange', this.state);

        if (this.id) {
            this.fields.forEach((fieldGroup) => {
                fieldGroup.fields.forEach((field) => {
                    if (field.preselectOption) {
                        field.preselectOption = false;
                    }
                });
            });
            this.load();
        }
        if (this.asideIndex !== -1) {
            this.$nextTick(() => {
                this.teleportButtons = true;
                // Log.info('teleportButtons set to true');
            });
        }
        this.$nextTick(() => {
            this.$forceUpdate();
        });
    },
    methods: {
        slotEmpty,
        isLastFieldGroup(index) {
            const lastKey = Object.keys(this.fields).length - 1;
            return index === lastKey;
        },
        load() {
            this.state.set(AsideState.LOADING);
            axios.get(`${this.url}/${this.id}`).then((response) => {
                this.$emit('update:modelValue', response.data);
                this.$emit('loaded', response.data);
                this.state.set(AsideState.IDLE);
            });
        },
        async submit() {
            var confirmed = true;

            if (this.saveModal) {
                var confirmed = await this.$dialogs[this.saveModal.type](
                    this.saveModal.title,
                    this.saveModal.message
                );
            }

            if (confirmed) {
                this.state.set(AsideState.SUBMITTING);

                // Clone the form
                let form = Object.assign({}, {...this.modelValue});

                // Filter all the fields for relationships and then loop through
                form = processFormRelationships(this.fields, form);

                axios[this.method](this.formUrl, form).then((response) => {
                    this.$emit('success', response.data);
                    this.state.set(AsideState.IDLE);
                }).catch((error) => {
                    this.state.set(AsideState.ERROR);
                    if (error.response) {
                        this.proxyError = error.response.data; // Error component should handle any error, JS or API
                        this.$nextTick(() => {
                            if (error.response.status === 422) {
                                this.scrollToError();
                            }
                        });
                    } else {
                        console.log(error);
                    }
                });
            }
        },
        scrollToError() {
            let found = false;
            this.fields.forEach((group) => {
                if (found === false) {
                    group.fields.forEach((field) => {
                        if (found === false) {
                            if (
                                this.proxyError != null &&
                                Object.prototype.hasOwnProperty.call(
                                    this.proxyError.errors,
                                    field.name
                                )
                            ) {
                                this.scroll(field.id);
                                found = true;
                            }
                        }
                    });
                }
            });
        },
        scroll(id) {
            this.$refs.root
                .querySelector(`#${id}`)
                .scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            setTimeout(() => {
                this.$refs.root?.querySelector(`#${id}`)?.classList?.add(
                        'bg-red-200',
                        'transition',
                        'ease-in-out',
                        'duration-1000'
                    );
                setTimeout(() => {
                    this.$refs.root?.querySelector(`#${id}`)?.classList?.remove('bg-red-200');
                }, 800);
                setTimeout(() => {
                    this.$refs.root?.querySelector(`#${id}`)?.classList?.remove(
                            'transition',
                            'ease-in-out',
                            'duration-1000'
                        );
                }, 1600);
            }, 300);
        },
    },
};
</script>
