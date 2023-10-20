<template>
    <Block class=""
        :class="{ 'w-full': content.width === 'full', 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8': content.width === 'standard', 'max-w-5xl mx-auto px-4 sm:px-6 lg:px-8': content.width === 'reduced', 'mb-0': content.bottom_space === 'none', 'mb-10': content.bottom_space === 'small', 'mb-16': content.bottom_space === 'medium', 'mb-24': content.bottom_space === 'large' }">
        <div class="pb-1 border-b-2">
            <p class="text-2xl  text-zinc-900 font-serif" :class="{
                'text-left': content.justify === 'left',
                'text-center': content.justify === 'centre',
                'text-right': content.justify === 'right'
            }" basic>
                {{ content.heading }}
            </p>
        </div>
    </Block>
</template>

<script lang="ts">

import { Block, BlockMixin, defineBlock, TextNode } from "@senses/builder";
import { defineComponent } from 'vue';

import H2Icon from '../../icons/svg/headings/heading-2.svg?raw';

export default defineComponent({
    components: { Block, TextNode },
    mixins: [BlockMixin],
    methods: {
        input(e) {
            const content = this.content;
            content.text = e.target.innerText;
            this.$emit('update:content', content)
        }
    },
    block: defineBlock({
        name: 'headings.heading-2',
        displayName: 'Heading 2',
        icon: H2Icon,
        categories: [
            'headings'
        ],
        defaultContent: {
            heading: 'Heading 2',
            justify: 'left',
            bottom_space: 'small',
            width: 'standard',
        },
        fields: [
            {
                name: 'heading',
                label: 'Heading',
                type: 'text',
            },
            {
                name: 'justify',
                label: 'Justify',
                type: 'select',
                options: [
                    {
                        label: 'Left',
                        value: 'left',
                    },
                    {
                        label: 'Centre',
                        value: 'centre',
                    },
                    {
                        label: 'Right',
                        value: 'right'
                    }
                ]
            },
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
    })
})
</script>
