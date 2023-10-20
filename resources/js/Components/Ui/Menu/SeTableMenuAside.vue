<template>
    <AsideLayout flush v-bind="$props" :loading="state.is(AsideState.LOADING)">
        <template #title>Table Settings</template>
        <template #actions>
            <ButtonGroup>
                <SecondaryButton @click="resetTableUserSetting">
                    <Icon icon="carbon:filter-remove" class="h-5 w-5" />
                </SecondaryButton>
            </ButtonGroup>
        </template>

        <div v-if="state.not(AsideState.LOADING)">
                <!-- #################################################### TABS ################################################### -->
                <div class="sticky top-0 left-0 z-10 w-full border-b bg-white">
                    <div>
                        <div class="sm:hidden">
                            <label for="tabs" class="sr-only">Select a tab</label>
                            <!-- Use an "onChange" listener to redirect the user to the selected tab URL. -->
                            <select id="tabs" name="tabs" class="block w-full rounded-md border-zinc-300 focus:border-primary-500 focus:ring-primary-500">
                                <option v-for="tab in tabs" :key="tab.name" :selected="schedulerMenuType === tab.name">
                                    {{ tab.name }}
                                </option>
                            </select>
                        </div>
                        <div class="hidden sm:block">
                            <nav class="flex space-x-4 p-4" aria-label="Tabs">
                                <a v-for="(tab, index) in tabs" :key="`schedulerButton${index}`" :class="[
                                        schedulerMenuType === tab.name
                                            ? 'bg-primary-700 text-white'
                                            : 'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-700',
                                        'group flex w-full cursor-pointer items-center rounded-md px-3 py-2 text-sm font-medium',
                                    ]" @click="schedulerMenuType = tab.name">
                                    <component :is="tab['icon']" class="mr-2 h-5 w-5" fill="#ffffff" />
                                    {{ tab.name }}</a>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- #################################################### DISPLAY ################################################### -->
                <div v-if="schedulerMenuType === 'Display'" class="justify-between p-4">
                    <MenuHeader key="rows-header" class="!border-none !p-0">
                        Number of Rows
                    </MenuHeader>
                    <div class="mt-4 grid grid-cols-1 gap-y-6 sm:grid-cols-4 sm:gap-x-4">
                        <PopoverMenuItem key="rows-1" class="relative flex cursor-pointer !items-start rounded-lg border bg-white !p-4 focus:outline-none" :class="{'border-primary-600': currentLimit == 25}" @click="
                                () => {
                                    this.data.table.setUserLimit(25);
                                    this.$asides.pop();
                                }
                            ">
                            <span class="flex h-full w-full items-center justify-center">
                                <span id="project-type-0-label" class="block text-lg font-medium text-zinc-700">25</span>
                            </span>
                            <CheckIcon class="h-6 w-6 text-primary-600" v-if="currentLimit == 25"/>
                        </PopoverMenuItem>
                        <PopoverMenuItem key="rows-2" class="relative flex cursor-pointer !items-start rounded-lg border bg-white !p-4 focus:outline-none" :class="{'border-primary-600': currentLimit == 50}" @click="
                                () => {
                                    this.data.table.setUserLimit(50);
                                    this.$asides.pop();
                                }
                            ">
                            <span class="flex h-full w-full items-center justify-center">
                                <span id="project-type-0-label" class="block text-lg font-medium text-zinc-700">50</span>
                            </span>
                            <CheckIcon class="h-6 w-6 text-primary-600" v-if="currentLimit == 50"/>
                        </PopoverMenuItem>
                        <PopoverMenuItem key="rows-3" class="relative flex cursor-pointer !items-start rounded-lg border bg-white !p-4 focus:outline-none" :class="{'border-primary-600': currentLimit == 75}" @click="
                                () => {
                                    this.data.table.setUserLimit(75);
                                    this.$asides.pop();
                                }
                            ">
                            <span class="flex h-full w-full items-center justify-center">
                                <span id="project-type-0-label" class="block text-lg font-medium text-zinc-700">75</span>
                            </span>
                            <CheckIcon class="h-6 w-6 text-primary-600" v-if="currentLimit == 75"/>
                        </PopoverMenuItem>
                        <PopoverMenuItem key="rows-4" class="relative flex cursor-pointer !items-start rounded-lg border bg-white !p-4 focus:outline-none" @click="
                                () => {
                                    this.data.table.setUserLimit(100);
                                    this.$asides.pop();
                                }
                            ">
                            <span class="flex h-full w-full items-center justify-center">
                                <span id="project-type-0-label" class="block text-lg font-medium text-zinc-700">100</span>
                            </span>
                            <CheckIcon class="h-6 w-6 text-primary-600" v-if="currentLimit == 100"/>
                        </PopoverMenuItem>
                        <PopoverMenuItem key="rows-5" class="relative flex cursor-pointer !items-start rounded-lg border bg-white !p-4 focus:outline-none sm:col-span-4" :class="{'border-primary-600': currentLimit == -1}" @click="
                                () => {
                                    this.data.table.setUserLimit(-1);
                                    this.$asides.pop();
                                }
                            ">
                            <span class="flex h-full w-full items-center justify-center">
                                <span id="project-type-0-label" class="block text-lg font-medium text-zinc-700">Fit to screen</span>
                            </span>
                            <CheckIcon class="h-6 w-6 text-primary-600" v-if="currentLimit == -1"/>
                        </PopoverMenuItem>
                    </div>
                </div>

                <!-- #################################################### COLUMNS ################################################### -->
                <div v-if="schedulerMenuType === 'Columns'" class="flex flex-col">
                    <div class="p-4">
                        <MenuHeader key="rows-header" class="!border-none !p-0">
                            Visible Columns
                        </MenuHeader>
                        <div class="mb-1 flex items-start pb-2 pt-4">
                            <div class="flex h-5 items-center">
                                <SeCheckbox
                                    id="visible_select_all"
                                    name="visible_select_all"
                                    :model-value="true"
                                    @change="toggleVisibility" />
                            </div>
                            <div class="text-sm-old ml-3">
                                <label for="visible_select_all" class="block text-zinc-700">
                                    Select All
                                </label>
                            </div>
                        </div>

                        <div class="relative mb-1 flex items-center py-2" v-if="fields[0] && fields[0].key == 'id'">
                            <LockClosedIcon class="h-4 w-4 shrink-0 text-zinc-400 group-hover:text-zinc-500"></LockClosedIcon>
                            <h3 class="ml-2 block capitalize">
                                {{
                                    fields[0].label
                                        .replaceAll('_', ' ')
                                        .replace('Id', 'ID')
                                }}
                            </h3>
                        </div>

                        <Sortable
                            :list="sortableFields"
                            item-key="key"
                            :options="{
                                fallbackOnBody: true,
                                animation: 150,
                                swapThreshold: 0.2,
                            }"
                            @end="onSortableEnd">
                            <template #item="{ element: element }">
                                <div class="relative mb-1 flex items-center py-2">
                                    <a href="#" class="group flex items-center border-transparent font-medium hover:bg-zinc-50">
                                        <ViewGridIcon class="h-4 w-4 shrink-0 text-zinc-400 group-hover:text-zinc-500"></ViewGridIcon>
                                    </a>
                                    <div class="ml-2 mr-4 flex h-5 items-center">
                                        <SeCheckbox
                                            v-if="element.key !== 'id'"
                                            :id="'visible_' + element.key"
                                            :model-value="
                                                isFieldVisible(element.key)
                                            "
                                            :name="'visible_' + element.key"
                                            @click="
                                                toggleFieldVisibility(
                                                    element.key
                                                )
                                            " />
                                    </div>
                                    <div class="text-sm-old">
                                        <label :for="'visible_' + element.key" class="block text-zinc-700 capitalize">
                                            {{
                                                element.label
                                                    .replaceAll('_', ' ')
                                                    .replace('Id', 'ID')
                                            }}
                                        </label>
                                    </div>
                                </div>
                            </template>
                        </Sortable>
                    </div>
                    <div class="sticky bottom-0 flex justify-end space-x-3 border-t bg-white p-4">
                        <SecondaryButton @click="$asides.pop()">Cancel</SecondaryButton>
                        <PrimaryButton :disabled="
                                createNew &&
                                creatingTitle &&
                                creatingTitle.length < 3
                            " @click="saveTemplate">Save Template</PrimaryButton>
                    </div>
                </div>

                <!-- #################################################### TEMPLATES ################################################### -->
                <div v-if="schedulerMenuType === 'Templates'" class="p-4">
                    <CollapseTransition>
                        <IndeterminateLoadingBar class="mb-4" v-if="loadingTemplates" />
                    </CollapseTransition>
                    <RadioGroup v-model="selectedTemplate" :disabled="loadingTemplates">
                        <div class="space-y-4">
                            <TableTemplateItem
                                v-for="(template, templateName) in templateData.templates"
                                :key="`tableTemplate${templateName}`"
                                :value="templateName"
                                :title="templateName"
                                @delete:template="removeTemplate">
                                <template #title>{{ templateName }}</template>
                                <template #subtitle>
                                    <div class="pt-1" v-if="template.description">
                                        {{ template.description }}
                                    </div>
                                    <div class="pt-1 text-zinc-400">
                                        <!-- <template v-if="template.hiddenFields && template.hiddenFields.length > 0"> -->
                                            {{ template.hiddenFields.length }} Hidden Columns
                                        <!-- </template> -->
                                        <template v-if="template.filters && template.filters.length > 0">
                                            <span aria-hidden="true">&middot;</span>
                                            Filtered by <span class="capitalize">{{ template.filters.map((filter) => filter.key).join(', ') }} </span>
                                        </template>
                                        <template v-if="template.sort">
                                            <span aria-hidden="true">&middot;</span>
                                            Sorted by <span class="capitalize">{{ template.sort }}</span>
                                        </template>
                                    </div>
                                </template>
                            </TableTemplateItem>
                        </div>
                    </RadioGroup>
                    <div class="mt-4 flex justify-end">
                        <SecondaryButton @click="$asides.push('TableTemplateAddForm', { setting: this.data.setting })">
                            Add New
                        </SecondaryButton>
                    </div>

                    <template v-if="presetTemplates && presetTemplates.length > 0 ">
                    <hr class="my-8"/>
                    <div class="space-y-4">
                        <MenuHeader key="rows-header" class="!border-none !p-0">
                            Select a preset template
                        </MenuHeader>
                        <div class="relative flex cursor-pointer rounded-lg border border-zinc-200 px-5 py-4 hover:bg-zinc-50 focus:outline-none" v-for="presetTemplate in presetTemplates" @click="addPresetTemplate(presetTemplate)">
                            <div class="grow">
                            <p class="font-medium text-zinc-900">{{ presetTemplate.title }}</p>
                            <p class="text-zinc-500">{{ presetTemplate.subTitle }}</p>
                            </div>
                            <div class="w-32 flex justify-end">
                                <SecondaryButton>Add Preset</SecondaryButton>
                            </div>
                        </div>
                    </div>
                    </template>
                </div>
        </div>
    </AsideLayout>
</template>

<script>
import CollapseTransition from '../Transitions/CollapseTransition.vue';
import IndeterminateLoadingBar from '../IndeterminateLoadingBar.vue';
import ButtonGroup from '../Buttons/ButtonGroup.vue';
import TableTemplateItem from '../../Senses/Tables/TableTemplateItem.vue';
import MenuHeader from './MenuHeader.vue';
import SecondaryButton from '../Buttons/SecondaryButton.vue';
import PopoverMenuItem from '../Popover/PopoverMenuItem.vue';
import AsideLayout from '../../Layout/AsideLayout.vue';
import axios from 'axios';
import AsideState from '../../../States/AsideState';

import {
    Icon
} from '@iconify/vue';

import {
    Sortable
} from 'sortablejs-vue3';
import {
    ViewGridIcon,
    LockClosedIcon,
    CheckIcon
} from '@heroicons/vue/solid';
import Aside from '../../../Mixins/Aside';
import PrimaryButton from '../../Ui/Buttons/PrimaryButton.vue';
import SeCheckbox from '../../Ui/Inputs/SeCheckbox.vue';

import {
    PresentationChartBarIcon,
    DuplicateIcon,
    TableIcon,
} from '@heroicons/vue/outline';

import {
    RadioGroup,
    RadioGroupLabel
} from '@headlessui/vue';
import EventHub from '../../../Support/EventHub';
import { onClientConfigLoad, getClientConfigTableTemplate } from '../../../Support/client';

export default {
    components: {
        AsideLayout,
        PopoverMenuItem,
        SecondaryButton,
        Icon,
        MenuHeader,

        LockClosedIcon,
        ViewGridIcon,
        PrimaryButton,
        SeCheckbox,
        Sortable,

        RadioGroup,
        RadioGroupLabel,
        EventHub,
        TableTemplateItem,
        // TableTemplateItem,
        ButtonGroup,
        CheckIcon,
        IndeterminateLoadingBar,
        CollapseTransition,
    },

    mixins: [Aside],

    data() {
        const currentTemplate = JSON.parse(
            JSON.stringify(
                this.data.table.templates[this.data.table.currentTemplateName]
            )
        );

        return {
            state: new AsideState(),
            AsideState,

            currentTemplate,
            currentTemplateName: this.data.table.currentTemplateName,
            createNew: false,
            creatingTitle: null,
            fields: JSON.parse(JSON.stringify(this.data.table.fields)),
            validation: null,

            selectedTemplate: null,

            templateData: [],
            loading: true,
            loadingTemplates: true,

            // Global Menu
            schedulerMenuType: 'Columns',
            tabs: [
                {
                    name: 'Columns',
                    icon: TableIcon,
                },
                {
                    name: 'Display',
                    icon: PresentationChartBarIcon,
                },
                {
                    name: 'Templates',
                    icon: DuplicateIcon,
                },
            ],

            error: null,
            presetTemplates:[]
        };
    },

    computed: {
        selectValue() {
            return this.createNew ? 'se-new-table-template' : this.data.table.currentTemplateName;
        },

        sortableFields() {
            if(this.fields[0]?.key == 'id') {
                return this.fields.slice(1);  //dumb thing to disallow reordering the first if id column, Matt wanted this.
            }
            return this.fields;
        },

        currentLimit() {
            return [25, 50, 75, 100].includes(this.currentTemplate?.limit) ? this.currentTemplate?.limit : -1;
        }
    },

    mounted() {
        EventHub.on(`${this.data.setting}-template-updated`, () => {
            this.load();
        });

        this.load();

        onClientConfigLoad(() => {
            this.presetTemplates = getClientConfigTableTemplate('task');
        });
    },

    watch: {
        selectedTemplate(value, oldValue) {
            if (oldValue === null) return;

            this.loadingTemplates = true;

            const { templateData } = this;

            templateData.currentTemplateName = value;

            this.currentTemplateName = value; //this is nonsense, we do we have so many copies that must be kept in sync???
            this.currentTemplate =  JSON.parse(JSON.stringify(this.data.table.templates[value]));

            axios
                // eslint-disable-next-line no-undef
                .patch(
                    `/api/v2/users/${user().id}/user-settings/${
                        this.data.setting
                    }`,
                    templateData
                )
                .then(() => {
                    this.loadingTemplates = false;
                    EventHub.emit(`${this.data.setting}-template-changed`);
                });
        },
    },

    methods: {
        setSchedulerMenu(name) {
            this.schedulerMenuType = name;
        },

        async resetTableUserSetting() {
            const confirmed = await this.$dialogs.confirm(
                'Reset Table Settings',
                'Are you sure you want to reset your settings for this table?'
            );

            if (confirmed) {
                axios
                    .delete(
                        `/api/v2/users/${user().id}/user-settings/${
                            this.data.setting
                        }`
                    )
                    .then(() => {
                        window.location.reload();
                    });
            }
        },

        removeTemplate(templateName) {
            this.loadingTemplates = true;

            var templates = this.templateData;

            delete templates['templates'][templateName];

            axios
                .patch(
                    `/api/v2/users/${user().id}/user-settings/${
                        this.data.setting
                    }`,
                    templates
                )
                .then(() => {
                    this.loadingTemplates = false;
                    EventHub.emit(`${this.data.setting}-template-changed`);
                });
        },

        onSortableEnd(event) {
            var oldIndex = event.oldIndex;
            var newIndex = event.newIndex;

            if (oldIndex === undefined || newIndex === undefined) {
                return;
            }

            if (this.fields[0]?.key == "id") {
                // This is because ID is locked and not in sortable fields
                // However it is still in this.fields meaning the indexes must be offset by one to account for the id "technically" being in the list

                oldIndex++;
                newIndex++;
            }

            const [removed] = this.fields.splice(oldIndex, 1);
            this.fields.splice(newIndex, 0, removed);
        },

        toggleVisibility(e) {
            const visible = e.target.checked;

            Object.keys(this.currentTemplate.fieldVisibility).forEach((key) => {
                if (key !== 'id') {
                    this.currentTemplate.fieldVisibility[key] = visible;
                }
            });
        },

        toggleFieldVisibility(fieldKey) {
            this.currentTemplate.fieldVisibility[fieldKey] = !this.currentTemplate.fieldVisibility[fieldKey];
        },

        isFieldVisible(fieldKey) {
            return (this.currentTemplate?.fieldVisibility[fieldKey] && this.currentTemplate?.fieldVisibility[fieldKey] == true);
        },

        // currentTemplateChange(e) {
        //     this.currentTemplate = JSON.parse(
        //         JSON.stringify(this.data.table.templates[e.target.value])
        //     );
        //     this.data.table.applyTemplate(e.target.value);
        // },

        saveTemplate() {
            this.data.table.setFields(this.fields);
            this.currentTemplate.fieldOrder = this.fields.map(
                (field) => field.key
            );

            this.data.table.saveNewTemplate(
                this.currentTemplateName,
                this.currentTemplate
            );
            this.$asides.pop();
        },

        load() {
            this.loadingTemplates = true;
            axios
                // eslint-disable-next-line no-undef
                .get(
                    `/api/v2/users/${user().id}/user-settings/${
                        this.data.setting
                    }`
                )
                .then((response) => {
                    if (response.data.data) {
                        this.templateData = response.data.data;
                        this.selectedTemplate = this.templateData.currentTemplateName;
                    }
                    this.loadingTemplates = false;
                });
        },

        addPresetTemplate(presetTemplate) {
            this.data.table.saveNewTemplate(
                presetTemplate.title,
                presetTemplate.template
            );

            this.$asides.pop();
        }
    },
};
</script>
