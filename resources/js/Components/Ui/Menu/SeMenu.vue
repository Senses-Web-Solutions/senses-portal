<template>
    <Menu as="div" class="relative text-left inline-block items-center" :class="button ? null : 'pt-1.5'">
        <div :class="!button ? 'inline-flex items-center justify-center' : null">
            <slot name="menuButton">
                <MenuButton class="md:inline-block items-center">
                    <component :is="button ? 'SecondaryButton' : 'div'" :rounded="rounded" :size="size">
                        <slot name="icon" v-if="$slots.icon"></slot>
                        <MenuIcon v-else class="w-5 h-5 mx-auto"/>
                    </component>
                </MenuButton>
            </slot>
        </div>

        <transition
            enter-active-class="transition duration-100 ease-out"
            enter-from-class="scale-95 opacity-0"
            enter-to-class="scale-100 opacity-100"
            leave-active-class="transition duration-75 ease-in"
            leave-from-class="scale-100 opacity-100"
            leave-to-class="scale-95 opacity-0"
        >
            <MenuItems
                class="absolute right-0 mt-2 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" style="z-index: 50 !important;" :class="style">
                <StaggeredListTransition>
                    <slot name="items"></slot> <!-- Menu Items -->
                </StaggeredListTransition>
            </MenuItems>
        </transition>
    </Menu>
</template>
<script>
import SecondaryButton from "../../Ui/Buttons/SecondaryButton.vue";
import {Menu, MenuItems, MenuButton} from "@headlessui/vue";
import {MenuIcon} from "@heroicons/vue/outline";
import StaggeredListTransition from '../../Ui/Transitions/StaggeredListTransition.vue';

export default {
    components: {
        SecondaryButton,
        MenuIcon,
        Menu,
        MenuItems,
        MenuButton,
        StaggeredListTransition,
    },
    props: {
        button: {
            type: Boolean,
            default: true,
        },
        rounded: {
            type: String,
            default: "full",
        },
        size: {
            type: String,
            default: 'md',
            validator(value) {
                return ['custom', 'xxs', 'xs', 'sm', 'md', 'lg', 'xl'].includes(
                    value
                );
            },
        },
        menuWidth:{
            type:String,
            default:'w-56'
        }
    },
    computed:{
        style() {
            return [this.menuWidth];
        }
    }
};
</script>
