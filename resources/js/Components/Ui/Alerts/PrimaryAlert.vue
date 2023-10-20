<template>
    <Alert :dismissable="dismissable" class="bg-primary-50" icon-class="text-primary-400" title-class="text-primary-800" text-class="text-primary-500" v-bind="$attrs">
        <template #icon>
            <slot name="icon">
            <BellIcon />
            </slot>
        </template>
        <template #title>
            <slot name="title"></slot>
        </template>
        <slot></slot>
    </Alert>
</template>
<script>
    import Alert from './Alert.vue';
    import { BellIcon } from '@heroicons/vue/solid';

    export default {
        components: {
            Alert, BellIcon
        },
        props: {
            dismissable: {
                default: false
            }
        }
    }
</script>
