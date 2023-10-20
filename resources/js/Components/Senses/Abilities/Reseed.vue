<template>
    <Card class="relative">
        <FadeTransition>
            <div
                v-if="!userSureDelayed"
                class="absolute inset-0 flex flex-col items-center py-16 space-y-6 rounded-lg backdrop-blur-lg"
            >
                <h2 class="text-4xl">Warning</h2>
                <p
                    class="text-xl"
                >This process affects system critical data. Do not proceed unless you know what you're doing.</p>
                <ThumbUpIcon
                    class="relative w-8 h-8 transition cursor-pointer"
                    :class="{
                        'text-green-500': userSure,
                        'text-red-500 rotate-180': !userSure
                    }"
                    @click="sure"
                ></ThumbUpIcon>
            </div>
        </FadeTransition>

        <div class="flex flex-col items-center py-16">
            <h2 class="mb-6 text-4xl">Click to Reseed Abilities</h2>

            <CollapseTransition>
                <div v-if="state.is(FormState.SUBMITTING)" class="pb-6">
                    <LoadingIcon class="w-8 h-8"></LoadingIcon>
                </div>
            </CollapseTransition>

            <PrimaryButton
                :disabled="state.is(FormState.SUBMITTING)"
                class="mb-6 last:mb-0"
                @click="reseed"
            >Reseed</PrimaryButton>

            <CollapseTransition>
                <DangerAlert v-if="response !== null && response !== 0">
                    <template #title>Process Exited with a Non-Zero exit code.</template>
                    The process exited with an exit code of
                    <strong>{{ response }}</strong>. Check the server logs for more details.
                </DangerAlert>
            </CollapseTransition>

            <CollapseTransition>
                <DangerAlert v-if="error !== null">
                    <template #title>Request failed.</template>
                    The server rejected the request, check your console for more details.
                </DangerAlert>
            </CollapseTransition>

            <CollapseTransition>
                <SuccessAlert v-if="response === 0">
                    <template #title>Permissions reseeded successfully.</template>
                </SuccessAlert>
            </CollapseTransition>
        </div>
    </Card>
</template>
<script>
import axios from 'axios';
import { ThumbUpIcon } from '@heroicons/vue/solid';
import LoadingIcon from '../../Ui/LoadingIcon.vue';
import Card from '../../Ui/Cards/Card.vue';
import FadeTransition from '../../Ui/Transitions/FadeTransition.vue';
import PrimaryButton from '../../Ui/Buttons/PrimaryButton.vue';
import FormState from '../../../States/FormState';
import CollapseTransition from '../../Ui/Transitions/CollapseTransition.vue';
import DangerAlert from '../../Ui/Alerts/DangerAlert.vue';
import SuccessAlert from '../../Ui/Alerts/SuccessAlert.vue';

export default {
    components: {
        Card,
        FadeTransition,
        ThumbUpIcon,
        PrimaryButton,
        LoadingIcon,
        CollapseTransition,
        DangerAlert,
        SuccessAlert,
    },
    data () {
        return {
            state: new FormState(),
            FormState,
            userSure: false,
            userSureDelayed: false,
            response: null,
            error: null
        }
    },
    mounted () {

    },
    methods: {
        sure () {
            this.userSure = true;
            setTimeout(() => {
                this.userSureDelayed = true;
            }, 300);
        },
        reseed () {
            this.response = null;
            this.error = null;
            this.state.set(FormState.SUBMITTING);
            axios.post('/api/v2/abilities/reseed').then((response) => {
                this.response = response.data;
                this.state.set(FormState.IDLE);
            }).catch((error) => {
                this.error = error.response.data;
                console.log(error.response.data);
                this.state.set(FormState.ERROR);
            });
        }
    }
}
</script>