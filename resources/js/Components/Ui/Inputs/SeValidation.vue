<template>
    <div>
        <slot></slot>
        <template v-if="hasError">
            <SeValidationMessage
                v-for="invalidMessage in invalidMessages"
                id="email-error"
                :key="invalidMessage"
            >{{ invalidMessage.replaceAll('.', ' ') }}</SeValidationMessage>
        </template>
    </div>
</template>
<script>
import SeValidationMessage from '../../Ui/Inputs/SeValidationMessage.vue';

export default {
    components: {
        SeValidationMessage,
    },
    props: {
        error: {
            type: Object,
            default: null,
        },
        name: {
            type: String,
            required: true,
        }
    },
    computed: {
        hasError() {
            return (
                this.error &&
                this.error.errors &&
                this.error.errors[this.name] &&
                this.error.errors[this.name][0]
            );
        },
        invalidMessages() {
            if (this.hasError) {
                return this.error.errors[this.name];
            }
            return ['invalid field'];
        },
    },
};
</script>
