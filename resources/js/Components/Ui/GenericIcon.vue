<template>
    <span class="inline-flex items-center justify-center rounded-full" :class="iconClass">
        <span class="font-medium leading-none" :class="'text-' + modelTextColour ">{{ modelInitials }}</span>
    </span>
</template>
<script>
export default {
    //todo swapout for user image?
    props:{
        model:{
            type:Object,
        },
        //optional for when you don't have an entire user object
        initials:{
            type:String
        },
        colour:{
            type:String
        },
        textColour:{
            type:String
        },
        size:{
            default:'sm'
        }
    },
    computed:{
        modelInitials() {
            if(this.model?.initials) {
                return this.model.initials;
            }
            if(this.initials) {
                return this.initials;
            }

            return null;
        },

        modelColour() {
            if(this.model?.colour) {
                return this.model.colour;
            }
            if(this.colour) {
                return this.colour;
            }

            return null;
        },

        modelTextColour() {
            //todo is this being shaded of colour with only storing tailwind colours?
            if(this.model?.text_colour) {
                return this.model.text_colour;
            }
            if(this.textColour) {
                return this.textColour;
            }

            return null;
        },

        iconClass() {
            const classes = ['bg-' + this.modelColour];
            const sizes = {
                'sm':'h-8 w-8',
                'md':'h-12 w-12',
            };

            if(sizes[this.size]) {
                classes.push(sizes[this.size]);
            }
            else {
                classes.push(sizes.sm);
            }

            return classes;
        }
    }
}
</script>
