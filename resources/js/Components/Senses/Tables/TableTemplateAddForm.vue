<template>
    <AsideLayout v-bind="$props">
        <template #title>Create Table Template</template>

        <Errors
            :dismissable="false"
            :error="computedError"
            class="mb-4"
        />

        <FormLayout>
            <template #icon>
                <InformationCircleIcon></InformationCircleIcon>
            </template>
            <template #title>Template Information</template>
            <template #description
                >Basic information about the template.</template
            >

            <FieldGroup
                v-model="form"
                v-model:fields="fields"
                :error="computedError"
            ></FieldGroup>

            <div class="flex justify-end gap-3">
                <SecondaryButton>Cancel</SecondaryButton>
                <PrimaryButton
                    :disabled="titleAlreadyExists || loading"
                    @click="submit"
                >
                    <template v-if="loading" #icon>
                        <LoadingIcon class="w-5 h-5"/>
                    </template>
                    Save
                </PrimaryButton>
            </div>
        </FormLayout>
    </AsideLayout>
</template>

<script>
import LoadingIcon from '../../Ui/LoadingIcon.vue';
import axios from 'axios';
import { InformationCircleIcon } from '@heroicons/vue/outline';
import AsideLayout from '../../Layout/AsideLayout.vue';
import FormLayout from '../../Layout/FormLayout.vue';
import FieldGroup from '../../Ui/FieldGroup.vue';
import Errors from '../../Ui/Errors/Errors.vue';
import PrimaryButton from '../../Ui/Buttons/PrimaryButton.vue';
import SecondaryButton from '../../Ui/Buttons/SecondaryButton.vue';
import Aside from '../../../Mixins/Aside';
import EventHub from '../../../Support/EventHub';

export default {
    components: {
        Errors,
        AsideLayout,
        FieldGroup,
        FormLayout,
        InformationCircleIcon,
        PrimaryButton,
        SecondaryButton,
        LoadingIcon,
    },

    mixins: [Aside],

    data() {
        return {
            selected: this.data.currentTemplateName,
            loading: true,
            templateData: [],
            fields: [
                {
                    type: 'text',
                    key: 'title',
                    name: 'title',
                    label: 'Title',
                },
                {
                    type: 'textarea',
                    key: 'description',
                    name: 'description',
                    label: 'Description',
                    helpText: 'Give a description about the template.',
                },
            ],
            form: {
                title: null,
                description: null,
            },
            error: null,
        };
    },

    computed: {
        titleAlreadyExists() {
            return (
                this.templateData &&
                this.templateData.templates &&
                Object.keys(this.templateData.templates).includes(
                    this.form.title
                )
            );
        },
        computedError() {
            if (this.titleAlreadyExists && !this.loading) {
                return {
                    errors: {
                        title: [
                            'A template with this title already exists for this table.',
                        ],
                    },
                };
            } else if (this.error) {
                return this.error;
            }

            return {};
        },
    },

    mounted() {
        axios
            // eslint-disable-next-line no-undef
            .get(
                `/api/v2/users/${user().id}/user-settings/${this.data.setting}`
            )
            .then((response) => {
                if (response.data.data) {
                    this.templateData = response.data.data;
                }
                this.loading = false;
            });
    },

    methods: {
        submit() {
            if (this.form.title) {
                this.loading = true;

                const { templateData } = this;
                templateData.templates[this.form.title.trim()] = this.data.setting != 'scheduler' ? {
                    sort: null,
                    order: 'desc',
                    filters: [],
                    hiddenFields: [],
                    fieldOrder: null,
                    description: this.form.description,
                } : {
                    lines: 2,
                    timeOfDay: 'day',
                    cellView: 'bars',
                    trackStyle: 'grid',
                    schedulerView: 'week',
                    resources: [],
                    assets: [],
                    users: [],
                    sources: [
                        'tasks',
                        'unavailabilities',
                        'holidays',
                        'absences',
                        'timesheets',
                        'asset-qualifications',
                        'user-qualifications',
                        'asset-maintenances'
                    ]
                };
                axios
                    // eslint-disable-next-line no-undef
                    .patch(
                        `/api/v2/users/${user().id}/user-settings/${
                            this.data.setting
                        }`,
                        templateData
                    )
                    .then(() => {
                        this.loading = false;
                        EventHub.emit(`${this.data.setting}-template-updated`);
                        this.$asides.pop();
                    })
                    .catch((error) => {
                        this.error = error.response.data;
                        this.loading = false;
                    });
            } else {
                this.error = {
                    errors: {
                        title: ['The title field is required.'],
                    },
                };
            }
        },
    },
};
</script>
