<template>
    <section>
        <div
            v-if="externalTitle && ($slots.title || $slots.actions)"
            class="flex items-center px-2 pb-4 lg:space-x-3"
            :class="classes"
        >
            <div>
                <SectionTitle>
                    <slot name="title"></slot>
                </SectionTitle>
                <!-- <p v-if="$slots.description" class="mt-1 text-zinc-500">
                    <slot name="description"></slot>
                </p> -->
            </div>
            <ButtonGroup v-if="$slots.actions">
                <slot name="actions"></slot>
            </ButtonGroup>
        </div>
        <div
            v-if="($slots.left || $slots.right)"
            class="flex justify-between items-end pb-4 lg:space-x-3"
            :class="classes"
        >
            <div>
                    <slot name="left"></slot>
            </div>
            <div v-if="$slots.right">
                <slot name="right"></slot>
            </div>
        </div>

        <!-- Note: Do not add overflow-hidden to div below, It breaks a lot of things -->
        <div
            class="rounded-lg bg-white"
            :class="{
                'shadow shadow-md': !internal,
                'border border-zinc-200': !borderless && !internal,
                'divide-y': !externalTitle
            }"
        >
            <div
                v-if="!externalTitle && ($slots.title || $slots.actions)"
                class="flex items-center p-4 lg:space-x-3"
                :class="classes"
            >
                <div>
                    <h3 class="text-xl leading-6 text-zinc-900">
                        <slot name="title"></slot>
                    </h3>
                    <!-- <p v-if="$slots.description" class="mt-1 text-zinc-500">
                            <slot name="description"></slot>
                        </p> -->
                </div>
                <ButtonGroup v-if="$slots.actions">
                    <slot name="actions"></slot>
                </ButtonGroup>
            </div>
            <div
                v-if="$slots.default"
                :class="{ 'p-5': !flush }"
            >
                <slot></slot>
            </div>
        </div>
        <div
            v-if="$slots.footer"
        >
            <slot name="footer"></slot>
        </div>
    </section>
</template>

<script>
import SectionTitle from '../Sections/SectionTitle.vue';
import ButtonGroup from '../Buttons/ButtonGroup.vue';

export default {
    components: {
        SectionTitle,
        ButtonGroup,
    },
    props: {
        flush: {
            type: Boolean,
            default: false,
        },
        internal: {
            type: Boolean,
            default: false,
        },
        headerClass: {
            type: String,
            default: '',
        },
        centreHeader: {
            type: Boolean,
            default: false,
        },
        borderless: {
            type: Boolean,
            default: false,
        },
        externalTitle: {
            type: Boolean,
            default: true,
        },
    },
    data() {
        return {
            isDark: window.localStorage.getItem('darkMode') === 'true',
        }
    },
    computed: {
        classes() {
            var classes = '';
            if (this.headerClass) {
                classes += ' ' + this.headerClass;
            }
            if (this.centreHeader) {
                classes += ' justify-center';
            } else {
                classes += ' justify-between';
            }
            return classes;
        },
    },
};
</script>
