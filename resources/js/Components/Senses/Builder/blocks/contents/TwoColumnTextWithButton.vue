<template>
    <Block class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"
        :class="{ 'mb-0': content.bottom_space == 'none', 'mb-10': content.bottom_space == 'small', 'mb-16': content.bottom_space == 'medium', 'mb-24': content.bottom_space == 'large' }">
        <div class="bg-white">
            <div class="">
                <div class="mx-auto max-w-2xl lg:mx-0 lg:max-w-none">
                    <p class="text-base font-semibold font-serif leading-7 text-indigo-600">{{ content.subheading }}</p>
                    <h1 class="mt-2 text-3xl font-medium font-serif tracking-tight text-zinc-700 sm:text-4xl">{{ content.heading }}</h1>
                    <div class="mt-10 grid max-w-xl grid-cols-1 gap-8 text-base leading-7 text-zinc-700 lg:max-w-none lg:grid-cols-2">

                        <p v-html="nl2br(content.col_1_text)" />
                        <p v-html="nl2br(content.col_2_text)" />

                    </div>
                    <div class="mt-10 flex">
                        <div
                            class="rounded-md bg-indigo-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">{{ content.button_text }}</div>
                    </div>
                </div>
            </div>

        </div>

    </Block>
</template>

<script>
import { defineComponent } from 'vue';
import { Block, BlockMixin, defineBlock, TextNode, } from "@senses/builder";
import axios from 'axios';
import Icon from '../../icons/svg/shark/wide-image-with-text.svg?raw';

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
        name: "contents.two-column-text-with-button",
        displayName: "Two Column Text with Button",
        categories: ["contents"],
        icon: Icon,
        defaultContent: {
            subheading: 'Subheading',
            heading: 'Heading',
            col_1_text: "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
            col_2_text: "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
            button_text: 'Button Text',
            button_url: "#",
            bottom_space: 'small'
        },
        fields: [
            {
                name: 'subheading',
                label: 'Sub-Heading',
                type: 'text',
            },
            {
                name: 'heading',
                label: 'Heading',
                type: 'text',
            },
            {
                name: 'col_1_text',
                label: 'Column 1 Text',
                type: 'textarea',
            },
            {
                name: 'col_2_text',
                label: 'Column 2 Text',
                type: 'textarea',
            },
            {
                name: 'button_text',
                label: 'Button Text',
                type: 'text',
            },
            {
                name: 'button_url',
                label: 'Button Url',
                type: 'text',
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
                    }
                ]
            }
        ],
    }),
});
</script>
