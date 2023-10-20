<template>
    <Block class=""
        :class="{ 'w-full': content.width === 'full', 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8': content.width === 'standard', 'max-w-5xl mx-auto px-4 sm:px-6 lg:px-8': content.width === 'reduced', 'mb-0': content.bottom_space == 'none', 'mb-10': content.bottom_space == 'small', 'mb-16': content.bottom_space == 'medium', 'mb-24': content.bottom_space == 'large' }">
        <div class="">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 gap-y-16">
                <div v-for="feature in content?.features">
                    <div class="max-w-3xl mx-auto lg:max-w-7xl px-4 sm:px-6 lg:px-8 text-center">
                        <TextNode basic
                            class="pointer break-words inline-block px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-700 hover:bg-blue-500 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
                            :model-value="feature.content.heading" @update:modelValue="update('heading', $event)"></TextNode>
                    </div>
                </div>
            </div>
        </div>
    </Block>
</template>

<script lang="ts">

import { Block, BlockMixin, defineBlock, TextNode, InternalDrop } from "@senses/builder";
import { defineComponent } from "vue";

import H1Icon from '../../icons/svg/3-buttons-dark.svg?raw';

export default defineComponent({
    components: {
        Block,
        TextNode
    },

    mixins: [BlockMixin],

    methods: {
        input(e) {
            const content = this.content;
            content.heading = e.target.innerText;
            this.$emit('update:content', content)
        }
    },

    block: defineBlock({
        name: 'repeaters.buttons-repeater',
        displayName: 'Buttons Grid',
        icon: H1Icon,

        defaultContent: {
            width: 'standard',
            bottom_space: 'small',
            features: [
                {
                    content: {
                        heading: "Button 1",
                        url: '#',
                    },
                },
                {
                    content: {
                        heading: "Button 2",
                        url: '#',
                    },
                },
                {
                    content: {
                        heading: "Button 3",
                        url: '#',
                    },
                },
            ]
        },

        categories: [
            'buttons'
        ],

        fields: [
            {
                name: 'features',
                label: 'Features',
                type: 'repeater',
                fields: [
                    {
                        name: 'heading',
                        label: 'Heading',
                        type: 'text',
                    },
                    {
                        name: 'url',
                        label: 'Url',
                        type: 'text',
                    },

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
        ],
    }),
})
</script>
