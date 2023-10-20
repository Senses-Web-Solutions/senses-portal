<template>
    <Alert id="danger_alert" :dismissable="dismissable" class="bg-red-100" icon-class="text-red-500" title-class="text-red-800" text-class="text-red-800" v-bind="$attrs">
        <template #icon>
            <slot name="icon">
            <XCircleIcon />
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
import { XCircleIcon } from '@heroicons/vue/solid';

export default {
    components: {
        Alert,
        XCircleIcon
    },
    props: {
        dismissable: {
            default: false
        }
    }
}
</script>
