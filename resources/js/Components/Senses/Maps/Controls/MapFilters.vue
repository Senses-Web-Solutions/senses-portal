<template>
    <div
        class="map-filter pointer-events-auto relative rounded bg-white shadow"
        :class="{
            'w-[462px]': visible,
            'max-w-[36px]': !visible,
            'max-w-[unset]': visible,
        }"
    >
        <div
            v-show="!visible"
            class="absolute -right-2 -top-2 z-50 flex h-4 w-4 items-center justify-center rounded-full bg-primary-700 text-center text-xs text-white dark:!text-white dark:bg-purple-600"
        >
            <span class="mt-0.5 block">{{ activeFilterCount }}</span>
        </div>
        <Tooltip>
            <button
                v-show="!visible"
                class="rounded bg-white p-2 text-gray-700 hover:bg-secondary-100 hover:text-gray-900 active:bg-secondary-300"
                @click="visible = !visible"
            >
                <FilterIcon class="h-5 w-5" />
            </button>
            <template #content>Data Filters</template>
        </Tooltip>
        <div
            v-show="visible"
            class="space-y-3 max-h-[calc(100vh-32px)] pt-4 px-4 overflow-y-scroll"
            :class="{ 'overflow-hidden': !visible }"
        >
            <SeSelectSearch
                v-for="filter in filters"
                :id="filter.id"
                :key="filter.id"
                :multiple="filter.multiple"
                :preload-options="filter.preloadOptions"
                :label="filter.label"
                :name="filter.id"
                :url="filter.url"
                :model-value="filter.value"
                :closeOnSelect="false"
                @update:modelValue="(e) => handleUpdate(filter, e)"
            ></SeSelectSearch>
            <div class="flex justify-end pt-2 pb-4">
                <PrimaryButton @click="apply">Apply</PrimaryButton>
            </div>
        </div>
    </div>

    <div
        v-if="!visible"
        class="map-filter pointer-events-auto relative max-h-[36px] rounded bg-white shadow"
    >
        <Tooltip>
            <!-- Show results length with amount of filters and small x button to clear -->
            <div class="flex items-center justify-between space-x-2 p-2">
                <div class="flex items-center">
                    <SeLabel
                        >{{ resultsLength }} results with
                        {{ activeFilterCount }} filter{{
                            activeFilterCount === 1 ? '' : 's'
                        }}</SeLabel
                    >
                </div>
                <PrimaryButton
                    v-show="activeFilterCount > 0"
                    size="xxs"
                    @click="clearFilters"
                >
                    <XIcon class="h-4 w-4" />
                </PrimaryButton>
            </div>

            <template #content> Clear Filters </template>
        </Tooltip>
    </div>
</template>


<script>

import axios from 'axios';
import { capitalize } from 'lodash-es';
import pluralize from 'pluralize';
import { FilterIcon, XIcon } from '@heroicons/vue/outline';

import SeSelectSearch from '../../../Ui/Inputs/SeSelectSearch.vue';
import SeLabel from '../../../Ui/Inputs/SeLabel.vue';
import PrimaryButton from '../../../Ui/Buttons/PrimaryButton.vue';
import Tooltip from '../../../Ui/Tooltip.vue';

import { getBackendClientConfig } from '../../../../Support/client';
import user from '../../../../Support/user';

export default {
    components: {
        FilterIcon,
        XIcon,
        SeSelectSearch,
        SeLabel,
        PrimaryButton,
        Tooltip,
    },

    props: {
        resultsLength: {
            type: Number,
            default: 0,
        },
    },

    emits: ['apply'],

    data() {
        return {
            visible: false,
            filters: [
                {
                    id: 'taskType.title',
                    label: 'Task Type',
                    url: '/api/v2/task-types?format=select-search',
                    multiple: true,
                    // preloadOptions: true,
                },
                {
                    id: 'workType.title',
                    label: 'Work Type',
                    url: 'api/v2/work-types?format=select-search',
                    multiple: false,
                    // preloadOptions: true,
                },
                {
                    id: 'task_tags',
                    label: 'Task Tags',
                    url: 'api/v2/tags?format=select-search&filter[tag-group-slug]=task',
                    multiple: true,
                    // preloadOptions: true,
                },
                {
                    id: `assignmentGroups.assignment_group_tags`,
                    label: 'Shift Tags',
                    url: 'api/v2/tags?format=select-search&filter[tag-group-slug]=assignment-group',
                    multiple: true,
                    // preloadOptions: true,
                },
                {
                    id: 'workPackage.title',
                    label: 'Work Package',
                    url: '/api/v2/work-packages?format=select-search',
                    multiple: true,
                    // preloadOptions: true,
                },
                {
                    id: 'project.title',
                    label: 'Project',
                    url: '/api/v2/projects?format=select-search',
                    multiple: true,
                    // preloadOptions: true,
                },
                {
                    id: 'depot.title',
                    label: pluralize(capitalize(getBackendClientConfig().terminology?.depot ?? 'depot')),
                    url: '/api/v2/depots?format=select-search',
                    multiple: true,
                    // preloadOptions: true,
                },
                {
                    id: 'company.title',
                    label: 'Companies',
                    url: '/api/v2/companies?format=select-search',
                    multiple: true,
                    // preloadOptions: true,
                },
                {
                    id: 'venue.title',
                    label: 'Venues',
                    url: '/api/v2/venues?format=select-search',
                    multiple: true,
                    // preloadOptions: true,
                },
                {
                    id: 'slaCode.title',
                    label: 'SLA Code',
                    url: '/api/v2/sla-codes?format=select-search',
                    multiple: true,
                    // preloadOptions: true,
                },
                {
                    id: 'status.title',
                    label: 'Status',
                    url: '/api/v2/statuses?format=select-search&filter[status-group-slug]=task',
                    multiple: true,
                    // preloadOptions: true,
                },
            ],

            currentTemplateName: 'Default',
            templates: {
                Default: null,
            },

            existingFilters: [],

            filtersToApply: {},
        };
    },

    computed: {
        activeFilterCount() {
            return Object.values(this.filtersToApply).filter(
                (filter) => filter.value
            ).length;
        },
        currentTemplate() {
            return this.templates[this.currentTemplateName];
        },
    },

    created() {
        this.loadUserSetting();
    },

    methods: {
        apply() {
            this.$emit('apply', this.filtersToApply);
            this.visible = false;
        },

        handleUpdate(filter, e) {
            const matchingFilter = this.existingFilters.find(
                (existingFilter) => existingFilter.field_key === filter.id
            );
            let selectedValues = Array.isArray(e)
                ? e.map((value) => value.title)
                : [e?.title ?? ''];

            if (matchingFilter) {
                selectedValues = [...selectedValues, ...matchingFilter.value];
            }

            this.filtersToApply[filter.id] = {
                field_key: filter.id,
                value: selectedValues.join(','),
            };
        },

        loadUserSetting() {
            axios.get(`/api/v2/users/${user().id}/user-settings/table-tasks`).then((response) => {
                this.templates = response.data.data.templates;

                this.filtersToApply = this.currentTemplate.filters.reduce(
                    (acc, existingFilter) => {
                        const matchingFilter = this.filters.find(
                            (filter) =>
                                filter.id === existingFilter.field_key
                        );

                        if (matchingFilter) {
                            let values =
                                typeof existingFilter.value === 'string'
                                    ? existingFilter.value.split(',')
                                    : existingFilter.value;

                            if (matchingFilter.multiple && values) {
                                values = values.map((value) => ({
                                    title: value,
                                }));
                            } else if (values) {
                                values = { title: values[0] };
                            }

                            matchingFilter.value = values;
                        }

                        acc[existingFilter.field_key] = {
                            field_key: existingFilter.field_key,
                            value: existingFilter.value,
                        };

                        return acc;
                    },
                    {}
                );

                this.apply();
            })
            .catch((error) => {
                console.error(error);
            });
        },

        clearFilters() {
            this.filtersToApply = {};

            // For all filters, clear the value
            this.filters.forEach((filter) => {
                filter.value = '';
            });

            this.$emit('apply', {});
        },
    },
};
</script>
<style scoped>
.map-filter {
    font-family: 'Inter', sans-serif;
}
</style>
