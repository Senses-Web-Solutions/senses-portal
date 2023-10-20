<template>
    <AsideLayout v-bind="$props" size="xs">
        <template #title>Update Table Columns</template>

        <div class="py-4">
            <div class="flex items-start py-2 mb-1">
                <div class="flex items-center h-5">
                    <SeCheckbox
                        id="visible_select_all"
                        name="visible_select_all"
                        :model-value="true"
                        @change="toggleVisibility" />
                </div>
                <div class="ml-3 text-sm-old">
                    <label for="visible_select_all" class="block text-zinc-700">
                        Select All
                    </label>
                </div>
            </div>

            <div class="relative flex items-center py-2 mb-1">
                <LockClosedIcon class="w-4 h-4 text-zinc-400 shrink-0 group-hover:text-zinc-500"></LockClosedIcon>
                <h3 class="ml-2 block">{{ this.fields[0].label.replaceAll('_', ' ').replace('Id', 'ID') }}</h3>
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
                <template #item="{element: element}">
                    <div class="relative flex items-center py-2 mb-1">
                        <a href="#" class="flex items-center font-medium border-transparent hover:bg-zinc-50 group">
                            <ViewGridIcon class="w-4 h-4 text-zinc-400 shrink-0 group-hover:text-zinc-500"></ViewGridIcon>
                        </a>
                        <div class="flex items-center h-5 ml-2 mr-4">
                            <SeCheckbox
                                v-if="element.key !== 'id'"
                                :id="'visible_' + element.key"
                                :model-value="isFieldVisible(element.key)"
                                :name="'visible_' + element.key"
                                @click="toggleFieldVisibility(element.key)" />
                        </div>
                        <div class="text-sm-old">
                            <label :for="'visible_' + element.key" class="block text-zinc-700">
                                {{ element.label.replaceAll('_', ' ').replace('Id', 'ID') }}
                            </label>
                        </div>
                    </div>
                </template>
            </Sortable>
        </div>

        <div class="py-5">
            <div class="flex justify-end space-x-3">
                <SecondaryButton @click="$asides.pop()">Cancel</SecondaryButton>
                <PrimaryButton :disabled="createNew && creatingTitle && creatingTitle.length < 3" @click="saveTemplate">Save Template</PrimaryButton>
            </div>
        </div>
    </AsideLayout>
</template>

<script>
import {
    Sortable
} from 'sortablejs-vue3';
import {
    ViewGridIcon,
    LockClosedIcon
} from '@heroicons/vue/solid';
import AsideLayout from '../../Layout/AsideLayout.vue';
import Aside from '../../../Mixins/Aside';
import SecondaryButton from '../../Ui/Buttons/SecondaryButton.vue';
import PrimaryButton from '../../Ui/Buttons/PrimaryButton.vue';
import SeCheckbox from '../../Ui/Inputs/SeCheckbox.vue';

export default {
    components: {
        LockClosedIcon,
        ViewGridIcon,
        AsideLayout,
        SecondaryButton,
        PrimaryButton,
        SeCheckbox,
        Sortable,
    },

    mixins: [Aside],

    data() {
        const currentTemplate = JSON.parse(
            JSON.stringify(
                this.data.table.templates[this.data.table.currentTemplateName]
            )
        );
        return {
            currentTemplate,
            currentTemplateName: this.data.table.currentTemplateName,
            createNew: false,
            creatingTitle: null,
            fields: JSON.parse(JSON.stringify(this.data.table.fields)),
            validation: null,
        };
    },

    computed: {
        selectValue() {
            return this.createNew ?
                'se-new-table-template' :
                this.data.table.currentTemplateName;
        },

        sortableFields() {
            return this.fields.slice(1); //dumb thing to disallow reordering the first column, Matt wanted this.
        }
    },

    watch: {
        'data.table.loading': function () {
            console.log(this.data.table.loading);
        },
    },

    methods: {
        onSortableEnd(event) {
            const {
                oldIndex,
                newIndex
            } = event;

            if (oldIndex === undefined || newIndex === undefined) {
                return;
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
            return (this.currentTemplate.fieldVisibility[fieldKey] && this.currentTemplate.fieldVisibility[fieldKey] == true);
        },

        currentTemplateChange(e) {
            this.currentTemplate = JSON.parse(
                JSON.stringify(this.data.table.templates[e.target.value])
            );

            this.data.table.applyTemplate(e.target.value);
        },

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
    },
};
</script>
