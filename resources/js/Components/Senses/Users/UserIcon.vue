<template>
    <span class="relative inline-flex items-center justify-center rounded-full" :class="classes">
        <span class="font-medium leading-none" :class="'text-' + userTextColour + ' text-' + textSize">
            {{ userInitials }}
        </span>
        <span v-if="user.active" class="right-0 bottom-0 absolute block h-2.5 w-2.5 rounded-full bg-green-400 ring-2 ring-white" />
    </span>
</template>
<script>
export default {
    //todo swapout for user image?
    props: {
        user: {
            type: Object,
        },
        height: {
            type: String,
            default: '8'
        },
        width: {
            type: String,
            default: '8'
        },
        textSize: {
            type: String,
            default: 'base',
            validator(value) {
                return ['xs', 'sm', 'base', 'lg', 'base-with-side'].includes(value);
            },
        },
        //optional for when you don't have an entire user object
        initials: {
            type: String
        },
        colour: {
            type: String
        },
        textColour: {
            type: String
        }
    },
    computed: {
        classes() {
            return 'bg-' + this.userColour + ' h-' + this.height + ' w-' + this.width;
        },

        userInitials() {
            if (this.user?.initials) {
                return this.user.initials;
            }
            if (this.initials) {
                return this.initials;
            }

            return null;
        },

        userColour() {
            if (this.user?.colour) {
                return this.user.colour;
            }
            if (this.colour) {
                return this.colour;
            }

            return null;
        },

        userTextColour() {
            //todo is this being shaded of colour with only storing tailwind colours?
            if (this.user?.text_colour) {
                return this.user.text_colour;
            }

            if (this.textColour) {
                return this.textColour;
            }

            return null;
        },
    }
}
</script>
