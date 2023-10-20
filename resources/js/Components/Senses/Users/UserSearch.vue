<template>
    <div class="space-y-2">
        <slot name="label" v-if="$slots.label"></slot>
        <div v-else>
            <SeLabel>Search for a User</SeLabel>
        </div>
        <SeSelectSearch
            v-if="type === 'internal'"
            :id="userType"
            textField="full_name"
            v-model="data"
            :field="field"
            :error="error"
            @update:model-value="userSelected"
            :name="userType"
            :url="'/api/v2/' + url"
            :preloadOptions="preloadOptions" />
        <CollapseTransition>
            <div v-if="type === 'external'" class="mt-2 space-y-2 rounded bg-zinc-100 p-4">
                <SeValidation :error="error" :name="validationField + '.first_name'" class="space-y-2">
                    <SeInput
                        label="First Name"
                        class="w-full"
                        v-model="data.first_name"
                        @blur="updateUser('first_name', data.first_name)"
                        :name="validationField + '.first_name'"
                        id="first_name"
                        :error="error" />
                </SeValidation>
                <SeValidation :error="error" :name="validationField + '.last_name'" class="space-y-2">
                    <SeInput
                        label="Last Name"
                        class="w-full"
                        v-model="data.last_name"
                        @blur="updateUser('last_name', data.last_name)"
                        :name="validationField + '.last_name'"
                        id="last_name"
                        :error="error" />
                </SeValidation>
                <SeValidation :error="error" :name="validationField + '.email'" class="space-y-2">
                    <SeInput
                        label="Email"
                        class="w-full"
                        v-model="data.email"
                        @blur="updateUser('email', data.email)"
                        :name="validationField + '.email'"
                        id="email"
                        :error="error" />
                </SeValidation>
                <SeValidation :error="error" :name="validationField + '.telephone'" class="space-y-2">
                    <SeInput
                        label="Telephone"
                        class="w-full"
                        v-model="data.telephone"
                        @blur="updateUser('telephone', data.telephone)"
                        :name="validationField + '.telephone'"
                        id="telephone"
                        :error="error" />
                </SeValidation>
                <SeValidation :error="error" :name="validationField + '.mobile'" class="space-y-2">
                    <SeInput
                        label="Mobile"
                        class="w-full"
                        v-model="data.mobile"
                        @blur="updateUser('mobile', data.mobile)"
                        :name="validationField + '.mobile'"
                        id="mobile"
                        :error="error" />
                </SeValidation>
                <SeValidation
                    v-if="registrationToggle"
                    :error="error"
                    :name="validationField + '.send_registration_email'"
                    class="space-y-2">
                    <SeToggle
                        label="Send Registration Email"
                        class="w-full"
                        v-model="data.send_registration_email"
                        @blur="
                            updateUser(
                                'send_registration_email',
                                data.send_registration_email
                            )
                        "
                        :name="validationField + '.send_registration_email'"
                        id="send_registration_email"
                        :error="error" />
                </SeValidation>
            </div>
        </CollapseTransition>
        <InfoAlert v-if="type == 'external'">
            <div class="flex justify-between" v-if="existType == 'similar' && existingUser">
                There is a similar user that already exists in the system.
                <SecondaryButton @click="userSelected(this.existingUser)">Use</SecondaryButton> <!-- Rob asked for this to be a button rather than a hyperlink -->
            </div>
            <div class="flex justify-between" v-else-if="existType == 'duplicate' && existingUser">
                '{{ existingUser.full_name }}' already exists in the system.
                <SecondaryButton @click="userSelected(this.existingUser)">Use</SecondaryButton> <!-- Rob asked for this to be a button rather than a hyperlink -->
            </div>
            <div v-else>
                This user does not yet exist in the system, so is fine to be created.
            </div>
        </InfoAlert>
    </div>
</template>

<script>
import SeSelectSearch from '../../Ui/Inputs/SeSelectSearch.vue';
import SeToggle from '../../Ui/Inputs/SeToggle.vue';
import SeInput from '../../Ui/Inputs/SeInput.vue';
import SeValidation from '../../Ui/Inputs/SeValidation.vue';
import SeLabel from '../../Ui/Inputs/SeLabel.vue';
import CollapseTransition from '../../Ui/Transitions/CollapseTransition.vue';
import SecondaryButton from '../../Ui/Buttons/SecondaryButton.vue';
import InfoAlert from '../../Ui/Alerts/InfoAlert.vue';
import axios from 'axios';

export default {
    components: {
        SeLabel,
        SeSelectSearch,
        SecondaryButton,
        SeValidation,
        InfoAlert,
        SeInput,
        SeToggle,
        CollapseTransition,
    },

    props: {
        type: {
            type: String,
            default: 'internal',
            validator(value) {
                return ['internal', 'external'].includes(value);
            },
        },
        id: {
            type: Number,
            default: null
        },
        userType: {
            type: String,
            default: "user_id",
        },
        disabled: {
            type: Boolean,
            default: false,
        },
        registrationToggle: {
            type: Boolean,
            default: false,
        },
        error: {
            required: false,
            default: null,
        },
        user: {
            default: null,
        },
        field: {
            type: String,
            default: 'id',
        },
        validationField: {
            type: String,
            default: 'user',
        },
        preloadOptions: {
            type: Boolean,
            default: false,
        },
        url: {
            type: String,
            default: 'users?format=select-search',
        }
    },

    mixins: [],

    data() {
        return {
            data: null,
            existingUser: null,
            existType: null,
        };
    },

    mounted() {
        if (this.userType && this.id) {
            axios.get(`/api/v2/users?format=select-search&filter[id_exact]=${this.id}`).then((response) => {
                this.userSelected(response.data[0]);
            });
        }
    },

    methods: {
        updateUser(key, value) {
            this.data['type'] = this.validationField;
            axios.post('/api/v2/user-search/', this.data).then((response) => {
                this.existingUser = response.data.user;
                this.existType = response.data.existType;
            });

            this.$emit('update', {
                key: key,
                value: value
            });
        },

        userSelected(event) {
            this.$emit('update', event);
        },
    },

    watch: {
        type() {
            this.data = this.user ?? null;
            this.existingUser = null;
            this.existType = null;
        },
        user() {
            if (!this.data) {
                this.data = this.user;
            }
        }
    },
};
//Generated 03-11-2021 09:59:19
</script>
