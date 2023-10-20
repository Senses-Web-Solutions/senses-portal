<template>
    <BaseDialog type="success" :resolve="resolve" :open="open">
         <template #title>
            {{ data.title }}
        </template>
        <template #subtitle>
            {{ data.subtitle }}
        </template>
        <template #button>
            I'm sure
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
