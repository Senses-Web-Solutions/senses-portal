<template>
    <Block :class="{ 'w-full': content.width === 'full', 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8': content.width === 'standard', 'max-w-5xl mx-auto px-4 sm:px-6 lg:px-8': content.width === 'reduced', 'mb-0': content.bottom_space === 'none', 'mb-10': content.bottom_space === 'small', 'mb-16': content.bottom_space === 'medium', 'mb-24': content.bottom_space === 'large' }">
        <div class="w-full" :class="{'text-left': content.justify_text === 'left', 'text-center': content.justify_text === 'center', 'text-right': content.justify_text === 'right',}">
            <p class="text-base font-semibold leading-7 font-serif text-indigo-600">{{content.subtitle}}</p>
            <h2 class="mt-2 text-3xl font-medium font-serif tracking-tight text-zinc-700 sm:text-4xl">{{ content.heading }}</h2>
            <p class="mt-6 text-lg leading-8 text-zinc-600">{{ content.text }}
            </p>
        </div>
    </Block>
</template>
<script>
import { defineComponent } from 'vue';
import { Block, BlockMixin, defineBlock, TextNode } from "@senses/builder";

import H1Icon from '../../icons/svg/text-category-title-with-sub.svg?raw';

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
        name: 'headings.heading-with-subtitle',
        displayName: 'Heading With Subtitle',
        icon: H1Icon,
        defaultContent: {
            heading: 'Heading',
            subtitle: 'Subtitle',
            text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            justify_text: 'center',
            bottom_space: 'small',
            width: 'standard',
        },
        categories: [
            'headings'
        ],

        fields: [
            {
                name: 'heading',
                label: 'Heading',
                type: 'text',
            },
            {
                name: 'subtitle',
                label: 'Subtitle',
                type: 'text',
            },
            {
                name: 'text',
                label: 'Text',
                type: 'text',
            },
            {
                name: 'justify_text',
                label: 'Justify Text',
                type: 'select',
                options: [
                    {
                        label: 'Left',
                        value: 'left',
                    },
                    {
                        label: 'Centre',
                        value: 'center',
                    },
                    {
                        label: 'Right',
                        value: 'right'
                    },
                ],
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
    })
})
</script>
