<template>
    <Alert :dismissable="dismissable" class="bg-success-50" icon-class="text-success-400" title-class="text-success-800"
           text-class="text-success-500" v-bind="$attrs">
        <template #icon>
            <slot name="icon">
            <CheckCircleIcon/>
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
import {CheckCircleIcon} from '@heroicons/vue/solid';

export default {
    components: {
        Alert, CheckCircleIcon
    },
    props: {
        dismissable: {
            default: false
        }
    }

}
</script>
