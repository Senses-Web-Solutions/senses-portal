<template>
    <Block :class="{ 'w-full': content.width === 'full', 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8': content.width === 'standard', 'max-w-5xl mx-auto px-4 sm:px-6 lg:px-8': content.width === 'reduced', 'mb-0': content.bottom_space == 'none', 'mb-10': content.bottom_space == 'small', 'mb-16': content.bottom_space == 'medium', 'mb-24': content.bottom_space == 'large' }">
        <figure class="border-l border-indigo-600 pl-9">
            <blockquote class="font-semibold text-zinc-900">
                <p v-html="nl2br(content.paragraph, true)"></p>
            </blockquote>
            <figcaption class="mt-6 flex gap-x-4">

                <div class="text-sm leading-6"><strong class="font-semibold text-zinc-900">{{ content.name }}</strong> – {{ content.subheader }}</div>
            </figcaption>
        </figure>
    </Block>
</template>

<script lang="ts">

import { Block, BlockMixin, defineBlock, TextNode } from "@senses/builder";
import { defineComponent } from "vue";

import H1Icon from '../../icons/svg/text-category-paragraph.svg?raw';

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
        name: 'contents.quote',
        displayName: 'Quote',
        icon: H1Icon,
        defaultContent: {
            paragraph: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Elementum sagittis vitae et leo duis ut.',
            name: 'John Doe',
            subheader: 'Holiday to Greece in 2023',
            bottom_space: 'small',
            width: 'standard',
        },
        categories: [
            'contents'
        ],
        fields: [
            {
                name: 'paragraph',
                label: 'Paragraph',
                type: 'textarea',
            },
            {
                name: 'name',
                label: 'Name',
                type: 'text',
            },
            {
                name: 'subheader',
                label: 'Subheader',
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
                            },

                        ]
                    },
                ]
            }
        ]
    })
})
</script>
