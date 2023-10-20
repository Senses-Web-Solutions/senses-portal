<template>
    <Alert :dismissable="dismissable" class="bg-warning-50" icon-class="text-warning-400" title-class="text-warning-800"
           text-class="text-warning-500" v-bind="$attrs">
        <template #icon>
            <slot name="icon">
            <ExclamationCircleIcon/>
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
import {ExclamationCircleIcon} from '@heroicons/vue/solid';

export default {
    components: {
        Alert, ExclamationCircleIcon
    },
    props: {
        dismissable: {
            default: false
        }
    }

}
</script>
