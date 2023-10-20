<template>
    <span
        :class="classes"
        :style="[
            hexColour ? 'background-color: ' + hexColour : null,
            hexColour ? 'color: ' + getTextColor(hexColour) : null,
        ]"
        class="inline-flex items-center rounded-full"
    >
        <slot></slot>
    </span>
</template>
<script>
export default {
    props: {
        size: {
            type: String,
            default: 'sm',
            validator(value) {
                return ['xs', 'sm'].includes(value);
            },
        },
        backgroundColour: {
            type: String,
            default: null,
            // todo validator - is one of enums??
        },
        textColour: {
            type: String,
            default: null,
        },
        colour: {
            // for when we only have one colour to base things from
            type: String,
            default: null,
        },
        hexColour: {
            type: String,
            default: null,
        },
        type: {
            type: String,
            validator(value) {
                return [
                    'primary',
                    'secondary',
                    'info',
                    'success',
                    'warning',
                    'danger',
                    'gray',
                ].includes(value);
            },
            default: null,
        },
        outline: {
            type: Boolean,
            default: false,
        },
    },
    methods: {
        getTextColor(bgColor) {
            var color =
                bgColor.charAt(0) === '#' ? bgColor.substring(1, 7) : bgColor;
            var r = parseInt(color.substring(0, 2), 16); // hexToR
            var g = parseInt(color.substring(2, 4), 16); // hexToG
            var b = parseInt(color.substring(4, 6), 16); // hexToB
            var uicolors = [r / 255, g / 255, b / 255];
            var c = uicolors.map((col) => {
                if (col <= 0.03928) {
                    return col / 12.92;
                }
                return Math.pow((col + 0.055) / 1.055, 2.4);
            });
            var L = 0.2126 * c[0] + 0.7152 * c[1] + 0.0722 * c[2];
            return L > 0.179 ? '#000' : '#fff';
        },
    },
    computed: {
        classes() {
            const classes = {
                xs: 'text-sm px-2.5 py-0.5',
                sm: 'px-3 py-0.5',
                primary: ' bg-primary-100 text-primary-800',
                secondary: ' bg-secondary-100 text-secondary-800',
                info: ' bg-info-100 text-info-800',
                danger: ' bg-danger-100 text-danger-800',
                warning: ' bg-warning-100 text-warning-800',
                success: ' bg-success-100 text-success-800',
                gray: ' bg-zinc-100 text-zinc-800',
            };
            if (this.outline) {
                classes.primary = ' border border-primary-300 text-primary-800';
                classes.secondary =
                    ' border border-secondary-300 text-secondary-800';
                classes.info = ' border border-info-300 text-info-800';
                classes.danger = ' border border-danger-300 text-danger-800';
                classes.warning = ' border border-warning-300 text-warning-800';
                classes.success = ' border border-success-300 text-success-800';
                classes.gray = ' border border-zinc-300 text-zinc-800';
            }
            let returnClasses = classes[this.size];
            if (this.type) {
                returnClasses += classes[this.type];
            } else if (this.backgroundColour) {
                if (this.outline) {
                    returnClasses += ` border border-${this.backgroundColour} text-${this.backgroundColour}`;
                } else {
                    returnClasses +=
                        ` bg-${this.backgroundColour} text-${this.textColour}` ??
                        'white';
                }
            } else if (this.colour && !this.outline) {
                returnClasses +=
                    ` bg-${this.colour} bg-opacity-10 text-${this.colour}` ??
                    'white';
            }
            return returnClasses;
        },
    },
};
</script>
