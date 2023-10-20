<template>
    <Block class=""
        :class="{ 'w-full': content.width === 'full', 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8': content.width === 'standard', 'max-w-5xl mx-auto px-4 sm:px-6 lg:px-8': content.width === 'reduced', 'mb-0': content.bottom_space === 'none', 'mb-10': content.bottom_space === 'small', 'mb-16': content.bottom_space === 'medium', 'mb-24': content.bottom_space === 'large' }">

        <div class="border-b-2">
            <h2 class="text-3xl text-zinc-400 font-serif font-light" :class="{
                'text-left': content.justify_text === 'left',
                'text-center': content.justify_text === 'centre',
                'text-right': content.justify_text === 'right'
            }" basic>
                £{{ content.price }}
            </h2>
        </div>
    </Block>
</template>
<script lang="ts">

import { defineComponent } from 'vue';
import { Block, BlockMixin, defineBlock, TextNode } from "@senses/builder";

import H1Icon from '../../icons/svg/headings/heading-1.svg?raw';

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
        name: 'contents.quote-price',
        displayName: 'Quote Price',
        icon: H1Icon,
        defaultContent: {
            price: '2000.00',
            justify_text: 'left',
            bottom_space: 'small',
            width: 'standard',
        },
        categories: [
            'contents'
        ],

        fields: [
            {
                name: 'price',
                label: 'Price',
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
                        value: 'centre',
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
