<template>
    <Collapse :rounded="false" :open="open" @click="open = !open" class="bg-white shadow">
        <template #title>Filters</template>
        <div class="grid grid-cols-3 gap-4" :id="model.slug">
            <SeDatePicker v-if="filters.includes('date')" id="date" v-model="criteria.date" label="Date" :full-width="true" :error="error" class="col-span-1" name="criteria.date" required />
            <SeDateRangePicker v-if="filters.includes('date_range')" id="date_range" v-model="criteria.date_range" label="Dates" :full-width="true" :error="error" class="col-span-1"
                name="criteria.date_range" required />
            <SeSelect clearable label="Time Format" v-if="filters.includes('time_format')" id="time_format" field="id" :error="error" class="col-span-1" name="criteria.time_format" :options="timeFormats" v-model="criteria.time_format" />
            <SeSelectSearch v-if="filters.includes('company')" id="company" v-model="criteria.company" field="id" clearable :error="error" preload-options
                :label="config.terminology?.company ?? 'Company'" class="col-span-1 capitalize" name="criteria.company" url="/api/v2/companies?format=select-search" />
            <SeSelectSearch v-if="filters.includes('companies')" :close-on-select="false" id="companies" v-model="criteria.companies" field="id" multiple clearable :error="error" preload-options
                :label="config.terminology?.company ?? 'Company'" class="col-span-1 capitalize" name="criteria.companies" url="/api/v2/companies?format=select-search" />
            <SeSelectSearch v-if="filters.includes('department')" id="department" clearable v-model="criteria.department" :error="error" field="id" preload-options
                class="col-span-1" label="Department" name="criteria.department" url="/api/v2/departments?format=select-search" />
            <SeSelectSearch v-if="filters.includes('departments')" :close-on-select="false" id="departments" clearable v-model="criteria.departments" multiple :error="error" field="id" preload-options
                class="col-span-1" label="Department" name="criteria.departments" url="/api/v2/departments?format=select-search" />
            <SeSelectSearch v-if="filters.includes('depot')" id="depot" v-model="criteria.depot" :error="error" field="id" clearable preload-options class="col-span-1" label="Depot" name="criteria.depot"
                url="/api/v2/depots?format=select-search" />
            <div class="space-y-1.5" v-if="filters.includes('depots')">
                <div class="flex justify-between space-x-2">
                    <SeLabel>Depot</SeLabel>
                    <div class="flex justify-start space-x-2">
                        <SecondaryButton size="xxs" @click="addAllDepots(true)">Add All</SecondaryButton>
                        <SecondaryButton size="xxs" @click="clearDepots(false)">Clear</SecondaryButton>
                    </div>
                </div>
                <SeSelect id="depots" multiple :error="error" :close-on-select="false" class="col-span-1" name="criteria.depots" :options="depots" v-model="selectedDepots" />
            </div>
            <SeSelectSearch v-if="filters.includes('task_type')" id="task_type" v-model="criteria.task_type" :error="error" field="id" clearable preload-options label="Task Type"
                class="col-span-1" name="criteria.task_type" url="/api/v2/task-types?format=select-search" />
            <SeSelectSearch v-if="filters.includes('task_types')" :close-on-select="false" id="task_types" v-model="criteria.task_types" multiple clearable :error="error" field="id" preload-options
                label="Task Type" class="col-span-1" name="criteria.task_types" url="/api/v2/task-types?format=select-search" />
            <SeSelectSearch v-if="filters.includes('work_stream')" id="work_stream" v-model="criteria.work_stream" :error="error" field="id" clearable preload-options
                label="Work Stream" class="col-span-1" name="criteria.work_stream" url="/api/v2/work-streams?format=select-search" />
            <SeSelectSearch v-if="filters.includes('work_streams')" :close-on-select="false" id="work_streams" v-model="criteria.work_streams" multiple clearable :error="error" field="id" preload-options
                label="Work Stream" class="col-span-1" name="criteria.work_streams" url="/api/v2/work-streams?format=select-search" />
            <SeSelectSearch v-if="filters.includes('work_package')" id="work_package" v-model="criteria.work_package" :error="error" clearable field="id" preload-options
                class="col-span-1" label="Work Package" name="criteria.work_package" url="/api/v2/work-packages?format=select-search" />
            <SeSelectSearch v-if="filters.includes('work_packages')" :close-on-select="false" id="work_packages" v-model="criteria.work_packages" multiple :error="error" clearable field="id"
                preload-options class="col-span-1" label="Work Package" name="criteria.work_packages" url="/api/v2/work-packages?format=select-search" />
            <SeSelectSearch v-if="filters.includes('project')" id="project" v-model="criteria.project" :error="error" clearable preload-options field="id" label="Project"
                class="col-span-1" name="criteria.project" url="/api/v2/projects?format=select-search" />
            <SeSelectSearch v-if="filters.includes('projects')" :close-on-select="false" id="projects" v-model="criteria.projects" multiple :error="error" clearable preload-options field="id"
                label="Project" class="col-span-1" name="criteria.projects" url="/api/v2/projects?format=select-search" />
            <SeSelectSearch v-if="filters.includes('venue')" id="venue" v-model="criteria.venue" :error="error" clearable field="id" preload-options label="Venue"
                class="col-span-1" name="criteria.venue" url="/api/v2/venues?format=select-search" />
            <SeSelectSearch v-if="filters.includes('venues')" :close-on-select="false" id="venues" v-model="criteria.venues" multiple clearable :error="error" field="id" preload-options label="Venue"
                class="col-span-1" name="criteria.venues" url="/api/v2/venues?format=select-search" />
            <SeSelectSearch v-if="filters.includes('asset_type')" id="asset_type" clearable v-model="criteria.asset_type" :error="error" field="id" preload-options
                label="Asset Type" class="col-span-1" name="criteria.asset_type" url="/api/v2/asset-types?format=select-search" />
            <SeSelectSearch v-if="filters.includes('asset_types')" :close-on-select="false" id="asset_types" clearable v-model="criteria.asset_types" multiple :error="error" field="id" preload-options
                label="Asset Type" class="col-span-1" name="criteria.asset_types" url="/api/v2/asset-types?format=select-search" />
            <SeSelectSearch v-if="filters.includes('user')" id="user" clearable v-model="criteria.user" :error="error" field="id" preload-options label="User"
                text-field="full_name" class="col-span-1" name="criteria.user" :url="userUrl" />
            <SeSelectSearch v-if="filters.includes('users')" :close-on-select="false" id="users" clearable v-model="criteria.users" multiple :error="error" field="id" text-field="full_name"
                preload-options label="User" class="col-span-1" name="criteria.users" :url="userUrl" />
        </div>

        <div class="flex justify-end pt-4">
            <PrimaryButton @click.self="runReport">Run</PrimaryButton>
        </div>
    </Collapse>
</template>

<script>
import SeDatePicker from '../../Ui/Inputs/SeDatePicker.vue';
import SeDateRangePicker from '../../Ui/Inputs/SeDateRangePicker.vue';
import SeSelectSearch from '../../Ui/Inputs/SeSelectSearch.vue';
import SeSelect from '../../Ui/Inputs/SeSelect.vue';
import SeLabel from '../../Ui/Inputs/SeLabel.vue';
import SecondaryButton from '../../Ui/Buttons/SecondaryButton.vue';
import PrimaryButton from '../../Ui/Buttons/PrimaryButton.vue';
import formatDateTime from '../../../Filters/FormatDateTime';
import { format, addDays } from 'date-fns';
import axios from 'axios';
import Collapse from '../../Ui/Collapse/Collapse.vue';
import { getBackendClientConfig } from '../../../Support/client';

export default {
    components: {
        SeDatePicker,
        SeSelect,
        SeLabel,
        SecondaryButton,
        SeDateRangePicker,
        SeSelectSearch,
        Collapse,
        PrimaryButton,
    },

    props: {
        filters: {
            type: Array,
            default: () => [],
        },
        error: {
            type: Object,
            default: null,
        },
        model: {
            type: Object,
            default: null,
        },
    },

    emits: ['submitCriteria'],

    data() {
        return {
            config: getBackendClientConfig(),
            user: window.user,
            defaultDate: format(new Date(), 'yyyy-MM-dd'),
            defaultEndDate: format(addDays(new Date(), 7), 'yyyy-MM-dd'),
            criteria: {},
            open: true,
            depots: [],
            defaults: null,
            selectedDepots: [],
            timeFormats: [
                { id: '00:00:00-23:59:59', title: '12pm to 12am' },
                { id: '06:00:00-05:59:59', title: '6am to 6pm' },
            ]
        };
    },

    mounted() {
        this.getDefaults();
        if(this.filters.includes('depot') || this.filters.includes('depots')){
            this.loadDepots();
        }
    },

    computed: {
        userUrl() {
            if (this.user().can('list-user')) {
                return '/api/v2/users?format=select-search';
            } else if (this.user().can('list-company-user')) {
                return '/api/v2/companies/' + this.user().company_id + '/users?format=select-search';
            }
        }
    },

    watch: {
        selectedDepots(newData, oldData) {
            this.criteria.depots = newData.map(a => a.id);
        }
    },

    methods: {
        getDefaults() {
            axios
                .get(
                    '/api/v2/users/' +
                    this.user().id +
                    '/default-report-layout-filters/' + this.model.id
                )
                .then((response) => {
                    this.defaults = response.data;
                    this.filters.forEach((filter) => {
                        if (filter == 'date') {
                            this.criteria[filter] = this.defaultDate;
                        } else if (filter == 'date_range') {
                            this.criteria[filter] = { 'start_date': this.defaultDate, 'end_date': this.defaultEndDate };
                        } else {
                            this.criteria[filter] = this.defaults[filter] ?? null;
                        }
                        if (this.criteria.depots) {
                            this.depots.forEach(depot => {
                                if (this.criteria.depots.includes(depot.id)) {
                                    this.selectedDepots.push(depot);
                                }
                            })
                        }
                    });
                    this.$emit('submitCriteria', this.criteria);
                });
        },

        addAllDepots() {
            this.selectedDepots = this.depots;
        },
        clearDepots() {
            this.selectedDepots = [];
        },

        loadDepots() {
            axios.get('/api/v2/depots?format=all').then((response) => {
                this.depots = response.data;
                if (this.criteria.depots) {
                    this.depots.forEach(depot => {
                        if (this.criteria.depots.includes(depot.id)) {
                            this.selectedDepots.push(depot);
                        }
                    })
                }
            });
        },
        formatDateTime,
        runReport() {
            var slug = this.model.slug;
            if (slug.startsWith('breakdown-')) {
                slug = 'breakdown';
            }
            this.$emit('submitCriteria', this.criteria);
            var proxyCriteria = Object.assign({}, this.criteria); //stupid js object references
            if (Object.keys(proxyCriteria).includes('date_range')) {
                delete (proxyCriteria['date_range']);
            }
            if (Object.keys(proxyCriteria).includes('date')) {
                delete (proxyCriteria['date']);
            }
            axios
                // eslint-disable-next-line no-undef
                .patch(`/api/v2/users/${user().id}/user-settings/builder-criteria-${slug}`, proxyCriteria)
                .then(() => {
                    // this.$notifications.push({
                    //     title: 'Report settings saved',
                    //     description: '',
                    //     type: 'success',
                    // });
                });
        },
    },
};
</script>
