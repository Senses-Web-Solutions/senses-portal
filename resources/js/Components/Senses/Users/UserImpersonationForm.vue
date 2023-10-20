<template>
    <AsideLayout v-bind="$props">
        <template #title>Impersonate User</template>
        <SensesForm v-model="setting"
            v-model:fields="fields"
            v-model:error="error"
            :url="url"
            save-text="Impersonate"
            v-if="showForm"
            :on-submit="submit"
            @state-change="state = $event"
        >
            <template #buttons>
                <SecondaryButton @click="clear" v-if="showClear"  :disabled="state.is(AsideState.SUBMITTING)">Clear Impersonation</SecondaryButton>
            </template>
        </SensesForm>
        <DangerAlert v-else>
            You do not have permission to impersonate users.
        </DangerAlert>
    </AsideLayout>
</template>

<script>
import AsideLayout from '../../Layout/AsideLayout.vue';
import SensesForm from '../Common/SensesForm.vue';
import Aside from '../../../Mixins/Aside';
import SecondaryButton from '../../Ui/Buttons/SecondaryButton.vue';
import DangerAlert from '../../Ui/Alerts/DangerAlert.vue';
import axios from 'axios';
import AsideState from '../../../States/AsideState';

export default {
    components: {
        DangerAlert,
        AsideLayout,
        SecondaryButton,
        SensesForm,
    },

    mixins: [Aside],

    data() {
        return {
            user: window.user,
            AsideState,
            state:new AsideState(),
            error:null,
            fields: [
                // todo organise form better
                {
                    title: 'Impersonate',
                    description: 'Select a user to impersonate',
                    fields:[
                        {
                            key: "user_id",
                            type: "select-search",
                            url: "api/v2/users?format=select-search",
                            label: "Select user",
                            textField: 'full_name',
                            field:'id'
                        },
                    ],
                },
            ],
            setting:{
                id:null,
                user_id:this.data.layout,
            },
            showClear:false
        };
    },
    created() {
        this.loadUserSetting();
    },
    computed:{
        url(){
            return '/api/v2/users/' + window.sensesActualCurrentUserID + '/user-settings/impersonation'
        },

        showForm() {
            return ((window.sensesActualCurrentUserID != this.user().id) || this.user().can('show-user-setting'));
        }
    },
    methods: {
        loadUserSetting() {
            axios.get(this.url)
                .then(response => {
                    this.setting = response.data.data;
                    this.showClear = this.setting.user_id != null;
                })
                .catch(error => {
                    if(error?.response?.status == 400) {
                        this.error = { message: 'Impersonation is not allowed for this user' };
                    }
                    console.log(error);
                });
        },
        clear() {
            this.setting.user_id = null;
            this.submit();
        },
        submit() {
            this.state.set(AsideState.SUBMITTING);
            axios.post(this.url, this.setting)
                .then(response => {
                    location.reload();
                    this.state.set(AsideState.IDLE);
                    this.showClear = this.setting.user_id != null;
                })
                .catch(error => {
                    this.state.set(AsideState.ERROR);
                    this.error = error;
                });
        }
    },
};
</script>
