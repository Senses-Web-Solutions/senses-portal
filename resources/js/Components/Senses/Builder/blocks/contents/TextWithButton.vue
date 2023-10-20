<template>
    <Block class=""
        :class="{ 'w-full': content.width === 'full', 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8': content.width === 'standard', 'max-w-5xl mx-auto px-4 sm:px-6 lg:px-8': content.width === 'reduced', 'mb-0': content.bottom_space == 'none', 'mb-10': content.bottom_space == 'small', 'mb-16': content.bottom_space == 'medium', 'mb-24': content.bottom_space == 'large' }">
        <div v-if="content.alignment === 'left'" class="grid grid-cols-2 gap-x-6">
            <p v-html="nl2br(content.paragraph, true)" class="cursor-text col-span-1" :class="{
                'text-left': content.justify_text === 'left',
                'text-center': content.justify_text === 'centre',
                'text-right': content.justify_text === 'right'
            }" basic />
            <div class="col-span-1 relative h-full">
                <div class="absolute transform -translate-y-1/2" :class="{
                    'left-0 top-1/2': content.justify_button === 'left',
                    'left-1/2 top-1/2 -translate-x-1/2': content.justify_button === 'centre',
                    'right-0 top-1/2': content.justify_button === 'right'
                }">
                    <div :class="'bg-' + content.button_colour, 'hover:bg-' + content.button_hover_colour, 'text-' + content.button_text_colour"
                        class="pointer inline-block px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        {{ content.button_text }}
                    </div>
                </div>
            </div>
        </div>
        <div v-if="content.alignment === 'right'" class="grid grid-cols-2 gap-x-6">
            <div class="col-span-1 relative h-full">
                <div class="absolute transform -translate-y-1/2" :class="{
                    'left-0 top-1/2': content.justify_button === 'left',
                    'left-1/2 top-1/2 -translate-x-1/2': content.justify_button === 'centre',
                    'right-0 top-1/2  ': content.justify_button === 'right'
                }">
                    <div :class="'bg-' + content.button_colour, 'hover:bg-' + content.button_hover_colour, 'text-' + content.button_text_colour"
                        class="pointer inline-block px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        {{ content.button_text }}
                    </div>
                </div>
            </div>
            <p v-html="nl2br(content.paragraph, true)" class="cursor-text col-span-1" :class="{
                'text-left': content.justify_text === 'left',
                'text-center': content.justify_text === 'centre',
                'text-right': content.justify_text === 'right'
            }" basic />
        </div>
    </Block>
</template>

<script lang="ts">

import { Block, BlockMixin, defineBlock, TextNode } from "@senses/builder";
import { defineComponent } from "vue";

import H1Icon from '../../icons/svg/shark/text-left-button-right.svg?raw';

export default defineComponent({
    components: { Block, TextNode },
    mixins: [BlockMixin],
    methods: {
        input(e) {
            const content = this.content;
            content.text = e.target.innerText;
            this.$emit('update:content', content)
        },
        nl2br(str, is_xhtml) {
            if (typeof str === 'undefined' || str === null) {
                return '';
            }
            var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
            return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
        }
    },
    block: defineBlock({
        name: 'contents.text-with-button',
        displayName: 'Text & Button',
        icon: H1Icon,
        defaultContent: {
            paragraph: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Elementum sagittis vitae et leo duis ut.',
            alignment: 'left',
            justify_text: 'centre',
            justify_button: 'centre',
            button_text: 'Button Text',
            button_colour: 'blue-500',
            button_hover_colour: 'blue-200',
            button_text_colour: 'gray-200',
            button_url: '#',
            width: 'standard',
            bottom_space: 'small',
        },
        categories: [
            'contents'
        ],
        fields: [
            {
                name: 'alignment',
                label: 'Alignment',
                type: 'select',
                options: [
                    {
                        label: 'Text Left, Button Right',
                        value: 'left',
                    },
                    {
                        label: 'Text Right, Button Left',
                        value: 'right',
                    },
                ]
            },
            {
                name: 'section1',
                label: 'Text',
                type: 'collapse',
                fields: [
                    {
                        name: 'paragraph',
                        label: 'Paragraph',
                        type: 'textarea',
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
                            }
                        ]
                    },
                ],
            },
            {
                name: 'section2',
                label: 'Button',
                type: 'collapse',
                fields: [
                    {
                        name: 'button_text',
                        label: 'Button Text',
                        type: 'text',
                    },
                    {
                        name: 'button_url',
                        label: 'Button URL',
                        type: 'text',
                    },
                    {
                        name: 'justify_button',
                        label: 'Justify Button',
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
                        name: 'colours',
                        label: 'Colours',
                        type: 'collapse',
                        fields: [
                            {
                                name: 'button_colour',
                                label: 'Button Colour',
                                type: 'colour'
                            },
                            {
                                name: 'button_hover_colour',
                                label: 'Button Hover Colour',
                                type: 'colour'
                            },
                            {
                                name: 'button_text_colour',
                                label: 'Button Text Colour',
                                type: 'colour'
                            }
                        ]
                    }
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
        ]
    })
})
</script>
