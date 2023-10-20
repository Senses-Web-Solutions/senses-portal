<template>
    <Block
        :class="{ 'w-full': content.width === 'full', 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8': content.width === 'standard', 'max-w-5xl mx-auto px-4 sm:px-6 lg:px-8': content.width === 'reduced', 'mb-0': content.bottom_space == 'none', 'mb-10': content.bottom_space == 'small', 'mb-16': content.bottom_space == 'medium', 'mb-24': content.bottom_space == 'large' }">
        <div class="focus:outline-none">
            <div class="grid grid-cols-3 gap-6">
                <InternalDrop v-model="content.col1" :key="blockKey" :is-editor="true"></InternalDrop>
                <InternalDrop v-model="content.col2" :key="blockKey" :is-editor="true"></InternalDrop>
                <InternalDrop v-model="content.col3" :key="blockKey" :is-editor="true"></InternalDrop>
            </div>
        </div>
    </Block>
</template>
<script lang="ts">
import { defineComponent } from 'vue';
import { Block, BlockMixin, defineBlock, TextNode, InternalDrop } from "@senses/builder";

import Icon from '../../icons/svg/layouts-three-equal-cols.svg?raw';

export default defineComponent({
    components: { Block, InternalDrop },
    mixins: [BlockMixin],
    block: defineBlock({
        name: 'layouts.three-equal-columns',
        displayName: 'Three Equal Columns',
        icon: Icon,
        categories: ['layouts'],
        defaultContent: {
            width: 'standard',
            bottom_space: 'small',
            col1: [],
            col2: [],
            col3: [],
        },
        fields: [
            {
                name: 'extras',
                label: 'Extras',
                type: 'collapse',
                fields: [
                    {
                        name: 'bottom_space',
                        label: 'Space Below',
                        type: 'select',
                        options: [
                            {
                                label: 'None',
                                value: 'none'
                            },
                            {
                                label: 'Small',
                                value: 'small'
                            },
                            {
                                label: 'Medium',
                                value: 'medium'
                            },
                            {
                                label: 'Large',
                                value: 'large'
                            },
                        ]
                    },
                    {
                        name: 'width',
                        label: 'Width',
                        type: 'select',
                        options: [
                            {
                                label: 'Full',
                                value: 'full'
                            },
                            {
                                label: 'Standard',
                                value: 'standard'
                            },
                            {
                                label: 'Reduced',
                                value: 'reduced'
                            }
                        ]
                    },
                ]
            }
        ]
    }),
})
</script>
