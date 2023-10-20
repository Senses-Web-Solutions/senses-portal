<template>
    <AsideLayout v-bind="$props" :loading="state.is(AsideState.LOADING)">
        <template #title>User Password Form</template>
        <div class="w-full space-y-5" v-if="state.not(AsideState.LOADING)">
            <Errors v-if="state.is(AsideState.ERROR)" :error="error" id="error"/>
            <SeInput v-model="user.password" label="Password" name="password" id="password" placeholder="Password" type="password" :error="error"/>
            <SeInput v-model="user.password_confirmation" label="Confirm Password" name="password_confirmation" id="password_confirmation" placeholder="Confirm Password" type="password" :error="error"/>
        </div>
        <template #footer>
            <div class="bg-white space-y-4">
                <div class="flex justify-end">
                    <ButtonGroup>
                        <SecondaryButton @click="$asides.pop()">Cancel</SecondaryButton>
                        <PrimaryButton type="primary" @click="submit"
                                       :disabled="state.is(AsideState.LOADING) || state.is(AsideState.SUBMITTING)">Save
                        </PrimaryButton>
                    </ButtonGroup>
                </div>
            </div>
        </template>
    </AsideLayout>
</template>

<script>
import AsideLayout from '../../Layout/AsideLayout.vue';
import SeInput from '../../Ui/Inputs/SeInput.vue';
import PrimaryButton from '../../Ui/Buttons/PrimaryButton.vue';
import ButtonGroup from '../../Ui/Buttons/ButtonGroup.vue';
import SecondaryButton from '../../Ui/Buttons/SecondaryButton.vue';
import Errors from '../../Ui/Errors/Errors.vue';
import Aside from '../../../Mixins/Aside';
import axios from "axios";
import AsideState from "../../../States/AsideState";

export default {
    components: {
        AsideLayout,
        SeInput,
        ButtonGroup,
        PrimaryButton,
        SecondaryButton,
        Errors
    },

    mixins: [Aside],

    data() {
        return {
            AsideState,
            state: new AsideState(),
            user: {
                password: null,
                password_confirmation: null,
            },
            error: null,
        };
    },

    computed: {},

    methods: {
        submit(){
            this.state.set(AsideState.SUBMITTING);
            axios.patch('/api/v2/users/' + this.data.id + '/password', this.user)
                .then((response) => {
                    this.formSuccess(response);
                })
                .catch((response) => {
                    this.formError(response);
                });
        },
        formSuccess(response) {
            this.state.set(AsideState.SUCCESS);
            this.$asides.pop();
        },
        formError(error) {
            this.state.set(AsideState.ERROR);
            this.error = error.response.data;
        },
    },
};
</script>
