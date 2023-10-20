<template>
    <div class="relative w-full rounded-full bg-zinc-200" :class="classes">
        <div
            :style="{
                width: width + '%',
                backgroundColour: colourIsHex ? colour : null,
            }"
            class="absolute rounded-full transition duration-100"
            :class="!colourIsHex ? colourClass : null, classes"
            :aria-valuenow="width"
            aria-valuemin="0"
            :aria-valuemax="currentWidth"
        ></div>
    </div>
    <!-- bg-primary-300 bg-secondary-300 bg-info-300 bg-success-300 bg-danger-300 bg-warning-300 -->
</template>
<script>
//todo make transition smoother (i'm not very good with transitions)
export default {
    props: {
        max: {
            type: Number,
            required: true,
        },
        current: {
            type: Number,
            default: 0,
        },
        colour: {
            type: String,
            validator(value) {
                return (
                    value.includes('#') ||
                    [
                        'primary',
                        'secondary',
                        'info',
                        'success',
                        'danger',
                        'warning',
                    ].includes(value)
                );
            },
            default: 'primary',
        },
        size: {
            type: String,
            validator(value) {
                return ['xs', 'sm', 'md', 'lg'].includes(value);
            },
            default: 'md',
        },
        hover: {
            type: Boolean,
            default: false,
        }
    },
    data() {
        return {
            width: 0,
            currentWidth: 0,
        };
    },
    mounted() {
        this.getCurrentWidth();
    },

    methods: {
        getCurrentWidth() {
            if (this.current == 0 && this.max == 0) {
                this.currentWidth = 0;
            } else if (this.current < this.max) {
                this.currentWidth = (this.current / this.max) * 100;
            } else if (this.current > this.max || this.current === this.max) {
                this.currentWidth = 100;
            }
            this.loadBar();
        },
        loadBar() {
            for (var i = 0; i <= this.currentWidth; i++) {
                this.width = i;
            }
        },
    },
    watch: {
        current() {
            this.getCurrentWidth();
        },
        max() {
            this.getCurrentWidth();
        },
    },
    computed: {
        colourIsHex() {
            return /^#[0-9A-F]{6}$/i.test(this.colour);
        },
        classes() {
            var classes = {
                xs: 'h-2',
                sm: 'h-3',
                md: 'h-4',
                lg: 'h-5',
            };
            if (this.size) {
                return classes[this.size];
            }
            return 'h-4';
        },
        colourClass() {
            var colourClass = 'bg-' + this.colour + '-300';
            if(this.hover){
                colourClass = colourClass + ' hover:bg-' + this.colour + '-500';
            }
            return colourClass;
        },
    },
};
</script>
