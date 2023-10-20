<template>
    <HeadlessCollapse v-if="slotEmpty($slots.default)" :open="group.active" hide-method="v-show">
        <template #header="{ isOpen }">
            <SidebarItem
                :title="title"
                arrow
                :open="isOpen"
                is-in-group>
                <template #icon>
                    <slot name="icon"></slot>
                </template>
            </SidebarItem>
        </template>

        <div class="pt-2">
            <slot></slot>
        </div>
    </HeadlessCollapse>
</template>

<script>
import {
    isFunction
} from 'lodash-es';
import HeadlessCollapse from '../../Ui/Collapse/HeadlessCollapse.vue';
import SidebarItem from './SidebarItem.vue';
import slotEmpty from '../../../Support/slotEmpty';

export default {
    components: {
        HeadlessCollapse,
        SidebarItem
    },
    provide() {
        return {
            group: this.group,
        };
    },
    inject: ['sidebar'],
    props: {
        title: {
            type: [String, Function],
            required: true,
        },
        open: {
            type: Boolean,
            default: false,
        }
    },

    data() {
        const {
            title
        } = this;
        const {
            icon
        } = this.$slots;

        return {
            group: {
                active: this.open,
                title,
                icon,
                items: []
            },
        };
    },
    computed: {
        groupTitle() {
            if (isFunction(this.title)) {
                return this.title();
            }
            return this.title;
        },
    },
    mounted() {
        this.sidebar.push(this.group);
    },
    methods: {
        slotEmpty,
    },
};
</script>
