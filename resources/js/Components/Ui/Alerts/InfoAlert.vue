<template>
    <Alert :dismissable="dismissable" class="bg-info-50" icon-class="text-info-400" title-class="text-info-800" text-class="text-info-500" v-bind="$attrs">
        <template #icon>
            <slot name="icon">
            <InformationCircleIcon/>
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
import {InformationCircleIcon} from '@heroicons/vue/solid';

export default {
    components: {
        Alert, InformationCircleIcon
    },
    props: {
        dismissable: {
            default: false
        }
    }
}
</script>
