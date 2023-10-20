<template>
    <div>
        <template v-for="block in content" :key="block.key">
            <component
                :is="resolveBlock(block.name)"
                :content="block.content"
                :block-key="block.key"
                :selected="false"
                :force-pointer-events="true"
                :data="data"
                :request-data="requestData"
                :is-editor="false"
            ></component>
        </template>
    </div>
</template>
<script setup>
import { useEditor } from '@senses/builder';

const props = defineProps({
    content: Array,
    blocks: Array,
    data: Object,
    requestData: Object,
});

const { resolveBlock, content } = useEditor(props);
</script>
