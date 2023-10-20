<template>
    <BaseDialog type="info" :resolve="resolve" :open="open" :hide-cancel="true">
        <template #title>
            {{ data.title }}
        </template>
        <template #subtitle>
            {{ data.subtitle }}
        </template>
        <template #button>
            Ok
        </template>
    </BaseDialog>
</template>
<script>
import BaseDialog from './Dialog.vue';

export default {
    components: {
        BaseDialog,
    },
    props: {
        resolve: Function,
        open: Boolean,
        data: Object
    },
}
</script>
