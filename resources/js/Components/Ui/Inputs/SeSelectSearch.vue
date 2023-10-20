<template>
    <!-- TODO: Update the placeholder text from  "list is empty" to something more friendly e.g. start typing to search -->
    <InputGroup :id="id" :label="label" :is-valid="isValid" inset-validation-icon>
        <Multiselect v-if="!permissionDenied" :id="id" ref="multiselect" v-model="value" :name="name" :custom-label="customLabel" :options="options" :internal-search="false" :show-labels="showLabels"
            :class="{ error: !isValid }" :loading="state.is(AjaxSelectState.LOADING)" :multiple="multiple" :track-by="trackBy"
            class="multiselect--rounded block w-full rounded-md focus:border-primary-500 focus:ring-4 focus:ring-primary-200" deselect-label="Deselect" select-label="" :disabled="disabled"
            :placeholder="placeholder" :group-select="groupSelect" :group-label="groupLabel" :group-values="groupValues" :hide-selected="hideSelected" :allow-empty="allowEmpty"
            :autocomplete="autocomplete" :close-on-select="closeOnSelect" @select="hasDoneInitialLoad = true" @search-change="findOptions($event)">
            <template #tag="props">
                <div v-if="!showLabels">
                    {{ '' }}
                </div>
                <div v-else class="bg-primary-500 text-white inline-block truncate rounded h-[22px] mr-2 mb-1">
                    <div class="flex items-center w-full h-full">
                        <div class="pl-2 pr-1">
                            {{ typeof props.option == "string" ? props.option : props.option[textField] }}
                        </div>
                        <div @mousedown.prevent="props.remove(props.option)" class="inline p-[3px] rounded hover:bg-primary-600 text-primary-700 hover:text-white">
                            <XIcon class="inline h-4 w-4"></XIcon>
                        </div>
                    </div>
                </div>
            </template>
            <template #noResult> No results found. </template>
            <template #noOptions> Enter a search query </template>
            <!-- <option
                v-if="placeholder"
                value=""
                disabled
                selected
            >
                {{ placeholder }}
            </option>
            <option
                v-for="option in options"
                :key="option.id"
                :value="option.id"
            >
                {{ option[textField] }}
            </option> -->
        </Multiselect>
        <div v-else>
            <div class="relative w-full leading-none text-danger-500">
                You do not have permission to view these options.
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                    <BanIcon class="h-5 w-5 text-danger-500" aria-hidden="true" />
                </div>
            </div>
        </div>
        <template #buttons v-if="clearable">
            <SecondaryButton size="xxs" class="py-0.25 px-1" @click="clearSelect">
                Clear
            </SecondaryButton>
        </template>
    </InputGroup>
</template>

<script>
import {
    BanIcon
} from '@heroicons/vue/solid';
import {
    debounce,
    cloneDeep,
    unionBy,
    get
} from 'lodash-es';
import Multiselect from 'vue-multiselect';
import {
    nextTick
} from 'vue';
import axios from 'axios';
import AjaxSelectState from '../../../States/AjaxSelectState';
import SecondaryButton from '../Buttons/SecondaryButton.vue';
import InputGroup from './InputGroup.vue';

import {
    XIcon
} from "@heroicons/vue/solid";

export default {
    components: {
        InputGroup,
        SecondaryButton,
        BanIcon,
        Multiselect,
        XIcon
    },
    props: {
        modelValue: {
            required: false,
            type: [Object, Array, String, Number, null],
        },
        url: {
            type: String,
            required: true,
        },
        label: {
            type: String,
            required: false,
            default: '',
        },
        name: {
            type: String,
            required: true,
        },
        id: {
            type: String,
            required: true,
        },
        error: {
            type: Object,
            required: false,
            default: () => ({}),
        },
        placeholder: {
            type: String,
            default: 'Please select',
        },
        textField: {
            type: String,
            default: 'title',
        },

        multiple: {
            type: Boolean,
            default: false,
        },
        field: {
            type: String,
            default: null,
        },
        searchKey: {
            type: String,
            default: 'search',
        },
        loadKey: {
            type: String,
            default: 'id_exact',
        },
        initialData: {
            type: [Object, Array, null],
            default: null,
        },
        trackBy: {
            type: String,
            default: 'id',
        },
        disabled: {
            type: Boolean,
            default: false,
        },
        preloadOptions: {
            type: Boolean,
            default: false,
        },
        preselectOption: {
            type: Boolean,
            default: false,
        },
        groupSelect: {
            type: Boolean,
            default: false,
        },
        hideSelected: {
            type: Boolean,
            default: false,
        },
        allowEmpty: {
            type: Boolean,
            default: true,
        },
        groupLabel: {
            type: String,
            default: null,
        },
        groupValues: {
            type: String,
            default: null,
        },
        autocomplete: {
            type: String,
            default: 'off',
        },
        clearable: {
            type: Boolean,
            default: false,
        },
        showLabels: {
            type: Boolean,
            default: true
        },
        closeOnSelect: {
            type: Boolean,
            default: true,
        }
    },
    emits: ['update:modelValue', 'permission-denied'],
    data() {
        const {
            modelValue
        } = this;
        return {
            AjaxSelectState,
            searchResponse: [],
            state: new AjaxSelectState(),
            value: cloneDeep(modelValue),
            hasDoneInitialLoad: !this.field, // if operating in model mode, don't load
            hasDoneInitialOptionsLoad: false,
            permissionDenied: false,
        };
    },

    computed: {
        isValid() {
            if (this.error && this.error.errors) {
                return !this.error.errors[this.name];
            }
            return true;
        },
        options() {
            let options = [];
            let value = [];
            if (this.value) {
                if (Array.isArray(this.value)) {
                    value = this.value;
                } else if (
                    typeof this.value === 'object' &&
                    Object.keys(this.value).length
                ) {
                    value = [this.value];
                }
            }

            if (this.searchResponse.length > 0 && value.length) {
                options = unionBy(this.searchResponse, value, this.trackBy);
            } else if (this.searchResponse.length > 0) {
                options = this.searchResponse;
            } else if (value.length) {
                options = value;
            }
            return options;
        },
    },
    watch: {
        isValid() {
            // the root element of vue-multiselect isnt the one with the border on.
            if (this.$refs.multiselect) {
                if (this.isValid) {
                    this.$refs.multiselect?.$el
                        .querySelector('.multiselect__tags')
                        .classList.remove('border-red-500');
                } else {
                    this.$refs.multiselect?.$el
                        .querySelector('.multiselect__tags')
                        .classList.add('border-red-500');
                }
            }
        },
        modelValue: {
            deep: true,
            handler(newValue) {
                if (!this.hasDoneInitialLoad) {
                    this.loadValue(newValue);
                }
                this.hasDoneInitialLoad = true;

                if (!this.field) {
                    this.value = newValue;
                } else {
                    // console.log('field set');
                }
            },
        },
        value(newValue, oldValue) {
            if (newValue?.id !== oldValue || !oldValue) {
                if (this.multiple) {
                    if (this.field && newValue?.length) {
                        this.$emit(
                            'update:modelValue',
                            newValue.map((value) => value[this.field])
                        );
                    } else {
                        this.$emit('update:modelValue', newValue);
                    }
                    return;
                }

                if (this.field) {
                    this.$emit(
                        'update:modelValue',
                        newValue ? newValue[this.field] : null
                    );
                } else {
                    this.$emit('update:modelValue', newValue);
                }
            }
        },
    },
    mounted() {
        if (this.initialData) {
            this.value = this.initialData;
        } else if (
            (typeof this.modelValue === 'string' ||
                typeof this.modelValue === 'number') &&
            this.modelValue
        ) {
            this.loadValue(this.modelValue);
        } else if (
            Array.isArray(this.modelValue) &&
            this.modelValue.length > 0
        ) {
            this.loadValue(this.modelValue);
        } else if (this.preloadOptions) {
            this.asyncFind('', null, true);
        }
        nextTick(() => {
            if (this.$refs.multiselect) {
                if (this.isValid) {
                    this.$refs.multiselect?.$el
                        .querySelector('.multiselect__tags')
                        .classList.remove('border-red-500');
                } else {
                    this.$refs.multiselect?.$el
                        .querySelector('.multiselect__tags')
                        .classList.add('border-red-500');
                }
            }
        });
    },
    methods: {
        debounce,
        clearSelect() {
            this.value = null;
        },
        loadOptions() {
            this.state.set(AjaxSelectState.LOADING);
            axios
                .get(this.url)
                .then((response) => {
                    if (this.multiple) {
                        this.searchResponse = response.data;
                    } else {
                        this.searchResponse = response.data.length ?
                            response.data[0] :
                            null;
                    }
                    this.hasDoneInitialOptionsLoad = true;
                    this.$nextTick(() => {
                        this.state.set(AjaxSelectState.IDLE);
                    });
                })
                .catch((error) => {
                    if (error.response.status === 403) {
                        // permission denied
                        this.permissionDenied = true;
                        this.$emit('permission-denied');
                    }
                    this.state.set(AjaxSelectState.ERROR);
                });
        },
        loadValue(id) {
            if (id && id != '') {
                var query = id;
                if (Array.isArray(id)) {
                    var ids = [];
                    Object.values(id).forEach((item) => {
                        if (typeof item == 'object') {
                            ids.push(item[this.trackBy]);
                        } else {
                            ids.push(item);
                        }
                    });
                    query = ids.join(',');
                }
                let {
                    url
                } = this;
                url = url.startsWith('/') ? url : `/${url}`;

                this.state.set(AjaxSelectState.LOADING);
                axios
                    .get(
                        `${url}${url.includes('?') ? '&' : '?'}filter[${this.loadKey}]=${query}`
                    )
                    .then(
                        debounce((response) => {
                            if (this.multiple) {
                                this.value = response.data;
                            } else {
                                this.value = response.data.length ?
                                    response.data[0] :
                                    null;
                            }
                            this.state.set(AjaxSelectState.IDLE);
                        }, 500)
                    )
                    .catch((error) => {
                        if (error.response.status === 403) {
                            // permission denied
                            this.permissionDenied = true;
                            this.$emit('permission-denied');
                        }
                        this.state.set(AjaxSelectState.ERROR);
                    });
            }
        },

        findOptions(searchQuery) {
            // We have to set the state to loading outside of the
            // debounced function, otherwise loading won't be set
            // until the user has stopped typing, meaning they'll
            // see "No Results Found"
            this.state.set(AjaxSelectState.LOADING);
            this.debouncedFindOptions(searchQuery);
        },

        // eslint-disable-next-line func-names
        debouncedFindOptions: debounce(function (searchQuery) {
            this.asyncFind(searchQuery, null, true);
        }, 500),

        asyncFind(query, url = null, force = false) {
            if (!query && !force) {
                return;
            }

            this.state.set(AjaxSelectState.LOADING);
            url = url ?? this.url;
            url = url.startsWith('/') ? url : `/${url}`;
            axios
                .get(
                    `${url}${this.url.includes('?') ? '&' : '?'}filter[${this.searchKey}]=${query}`
                )
                .then((response) => {
                    this.searchResponse = response.data;
                    this.$nextTick(() => {
                        this.state.set(AjaxSelectState.IDLE);
                        // If there is only one option and preloadOptions is true, then auto fill the select with that one option
                        if (
                            !this.hasDoneInitialOptionsLoad &&
                            this.preloadOptions &&
                            this.preselectOption &&
                            response.data.length === 1
                        ) {
                            console.log(this.value);
                            [this.value] = response.data;
                            this.hasDoneInitialOptionsLoad = true;
                        } else if (!this.hasDoneInitialOptionsLoad &&
                            this.preloadOptions &&
                            this.preselectOption &&
                            response.data.length > 1) {
                            [this.value] = [this.searchResponse[0]];
                            this.hasDoneInitialOptionsLoad = true;
                        }
                        this.hasDoneInitialOptionsLoad = true;
                    });
                })
                .catch((error) => {
                    if (error.response.status === 403) {
                        // permission denied
                        this.permissionDenied = true;
                        this.$emit('permission-denied');
                    }
                    this.state.set(AjaxSelectState.ERROR);
                });
        },
        customLabel(item) {
            return get(item, this.textField);
        },
    },
};
</script>
