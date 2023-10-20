<template>
    <component :is="disabled ? 'Tooltip' : 'div'">
        <SecondaryButton
            :disabled="disabled"
            @click="buttonClicked"
        >
            {{text}}
        </SecondaryButton>
        <template
            v-if="disabled"
            #content
            >Permission denied</template
        >
    </component>
</template>
<script>
// @todo move to Senses/
import SecondaryButton from './SecondaryButton.vue';
import Tooltip from '../Tooltip.vue';

export default {
    components: { Tooltip, SecondaryButton },
    props: {
        form: {
            type: String,
            default: null,
        },
        link: {
            type: String,
            default: null,
        },
        model: {
            type: String,
            default: null,
        },
        asideData: {
            type: Object,
            default: () => ({}),
        },
        text: {
            type: String,
            default: 'Add'
        }
    },
    emits: ['click'],
    data() {
        return {
            user: window.user,
        };
    },

    computed: {
        disabled() {
            return !this.user().can(`create-${this.model}`);
        },
    },

    methods: {
        buttonClicked(event) {
            if (this.form) {
                this.$asides.push(this.form, this.asideData);
            } else if (this.link) {
                window.location.href = this.link;
            } else {
                this.$emit('click', event);
            }
        },
    },
};
</script>
