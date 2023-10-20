<template>
    <AsideLayout v-bind="$props" size="sm">
        <template #title>Table Templates</template>

        <template #actions>
            <SecondaryButton 
                @click="$asides.push('TableTemplateAddForm', {
                    setting: data.setting
                })"
            >Add New</SecondaryButton>
        </template>

        <RadioGroup v-model="selected" :disabled="loading">
            <RadioGroupLabel class="sr-only">Server size</RadioGroupLabel>
            <div class="space-y-2">
                <TableTemplateItem
                    v-for="(template, templateName) in templateData.templates"
                    :key="`tableTemplate${templateName}`"
                    :value="templateName"
                >
                    <template #title>{{ templateName }}</template>
                    <template #subtitle>
                        <span>{{ template.hiddenFields.length }} Hidden Columns</span>
                        <template v-if="template.filters && template.filters.length > 0">
                            <span aria-hidden="true">&middot;</span>
                            <span>Filtered by <span class="capitalize">{{ template.filters.map(filter => filter.key).join(', ') }}</span></span>
                        </template>
                        <template v-if="template.sort">
                            <span aria-hidden="true">&middot;</span>
                            <span>Sorted by <span class="capitalize">{{ template.sort }}</span></span>
                        </template>
                    </template>
                </TableTemplateItem>
            </div>
        </RadioGroup>
    </AsideLayout>
</template>

<script>
import axios from 'axios';
import {
    RadioGroup,
    RadioGroupLabel
} from '@headlessui/vue'
import SecondaryButton from '../../Ui/Buttons/SecondaryButton.vue';
import AsideLayout from '../../Layout/AsideLayout.vue';
import Aside from '../../../Mixins/Aside';
import EventHub from '../../../Support/EventHub';
import TableTemplateItem from './TableTemplateItem.vue';

export default {
    components: {
        RadioGroup,
        RadioGroupLabel,
        AsideLayout,
        TableTemplateItem,
        SecondaryButton
    },

    mixins: [Aside],

    data () {
        return {
            selected: this.data.currentTemplateName,
            templateData: [],
            loading: true,
        }
    },

    watch: {
        selected(v) {
            this.loading = true;
            
            const { templateData } = this;

            templateData.currentTemplateName = v;

            axios
                // eslint-disable-next-line no-undef
                .patch(`/api/v2/users/${user().id}/user-settings/${this.data.setting}`, templateData)
                .then(() => {
                    this.loading = false;
                    EventHub.emit(`${this.data.setting}-template-changed`);
                    this.$asides.pop();
                });
        }
    },

    mounted () {
        EventHub.on(`${this.data.setting}-template-updated`, () => {
            this.load();
        });

        this.load();
    },

    methods: {
        load() {
            this.loading = true;
            axios
                // eslint-disable-next-line no-undef
                .get(`/api/v2/users/${user().id}/user-settings/${this.data.setting}`)
                .then((response) => {
                    if (response.data.data) {
                        this.templateData = response.data.data;
                    }
                    this.loading = false;
                })
        }
    }
};
</script>