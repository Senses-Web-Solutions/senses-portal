<template>
    <AsideLayout v-bind="$props">
        <template #title>Update Contact Settings</template>
        <SensesForm
            v-model="contactSettings"
            v-model:fields="fields"
            :url="url"
            :aside-index="asideIndex"
            @success='formSuccess'>
            <template #contact_settings>
                <div v-if="contactSettingGroups" v-for="(settings, groupName) in contactSettingGroups" class="divide-y">
                    <Collapse flush highlight class="overflow-hidden my-4 rounded-lg border" headerClass="p-4 rounded-lg">
                        <template #title>{{ titleCase(groupName) }}</template>
                        <div class="flex justify-between items-center space-x-4 p-4 first:border-none border-t">
                            <BoldText>Notification</BoldText>
                            <div class="flex space-x-8">
                                <BoldText>Web</BoldText>
                                <BoldText>Email</BoldText>
                                <BoldText>SMS</BoldText>
                            </div>
                        </div>
                        <div v-for="(options, setting) in settings" class="flex justify-between items-center space-x-4 p-4 first:border-none border-t">
                            <StrongText>{{ titleCase(setting) }}</StrongText>
                            <div class="flex space-x-4">
                                <SeToggle
                                    :id="setting + 'web'"
                                    :name="setting + '-web'"
                                    v-model="contactSettings[setting]['web']" />
                                <SeToggle
                                    :id="setting + 'email'"
                                    :name="setting + '-email'"
                                    v-model="contactSettings[setting]['email']" />
                                <SeToggle
                                    :id="setting + 'sms'"
                                    :name="setting + '-sms'"
                                    v-model="contactSettings[setting]['sms']" />
                            </div>
                        </div>
                    </Collapse>
                </div>
            </template>
        </SensesForm>
    </AsideLayout>
</template>

<script>
import Collapse from '../../Ui/Collapse/Collapse.vue';
import BoldText from '../../Ui/Text/BoldText.vue';
import StrongText from '../../Ui/Text/StrongText.vue';
import SeToggle from '../../Ui/Inputs/SeToggle.vue';
import AsideLayout from '../../Layout/AsideLayout.vue';
import SensesForm from '../Common/SensesForm.vue';
import Aside from '../../../Mixins/Aside';
import axios from 'axios';
import AsideState from '../../../States/AsideState';
import titleCase from '../../../Filters/TitleCase.js'

export default {
    components: {
        AsideLayout,
        SensesForm,
        SeToggle,
        StrongText,
        BoldText,
        Collapse,
    },

    mixins: [Aside],
    props: {
        data: {
            type: Object,
            required: true,
        },
    },
    mounted() {
        this.load();
    },
    data() {
        return {
            AsideState,
            state: new AsideState(),
            contactSettings: [],
            contactSettingGroups: {
                'user': {},
                'assignment-group': {},
                'holiday': {},
                'to-do': {},
                'misc': {}
            },
            // error:null,
            fields: [{
                title: 'Contact Settings',
                description: 'Select which notifications user will receive.',
                fields: [{
                    key: 'contact_settings',
                    type: 'template',
                }, ],
            }, ],
        };
    },
    computed: {
        url() {
            return '/api/v2/users/' + this.data.id + '/user-settings/contact-settings';
        },
    },
    methods: {
        titleCase,
        formSuccess(response) {
            this.state.set(AsideState.SUCCESS);
            this.$asides.pop();
        },
        load() {
            this.state.set(AsideState.LOADING);
            axios
                .get('/api/v2/users/' + this.data.id + '/user-settings/contact-settings')
                .then((response) => {
                    this.contactSettings = response.data.data;
                    for (const [settingsKey, setting] of Object.entries(this.contactSettings)) {
                        let foundGroup = false;
                        for (const [groupKey, group] of Object.entries(this.contactSettingGroups)) {
                            if (settingsKey.includes(groupKey)) {
                                this.contactSettingGroups[groupKey][settingsKey] = setting;
                                foundGroup = true;
                            }
                        }
                        if (!foundGroup) {
                            this.contactSettingGroups["misc"][settingsKey] = setting;
                        }
                    }

                    this.state.set(AsideState.IDLE)
                });
        },
    }
};
</script>
