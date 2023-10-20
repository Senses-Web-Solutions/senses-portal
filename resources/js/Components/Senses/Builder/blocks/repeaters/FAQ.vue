<template>
    <Block :class="{ 'w-full': content.width === 'full', 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8': content.width === 'standard', 'max-w-5xl mx-auto px-4 sm:px-6 lg:px-8': content.width === 'reduced', 'mb-0': content.bottom_space == 'none', 'mb-10': content.bottom_space == 'small', 'mb-16': content.bottom_space == 'medium', 'mb-24': content.bottom_space == 'large' }">
        <div class="bg-white">
            <div class="divide-y divide-zinc-900/10 ">
                <h2 class="text-2xl font-medium font-serif tracking-tight text-zinc-700 sm:text-4xl mb-5">{{ content.title }}</h2>
                <dl v-for="feature in content?.features" class="first:mt-10 space-y-8 divide-y divide-zinc-900/10">
                    <div class="py-8 lg:grid lg:grid-cols-12 lg:gap-8">
                        <dt class="text-base font-semibold leading-7 text-zinc-900 lg:col-span-5">{{ feature.content.heading }}</dt>
                        <dd class="lg:col-span-7 lg:mt-0">
                            <p class="text-base leading-7 text-zinc-600">{{ feature.content.answer }}</p>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </Block>
</template>
<script lang="ts">

import { Block, BlockMixin, defineBlock, TextNode } from "@senses/builder";

import H1Icon from '../../icons/svg/faq.svg?raw';

export default {
    components: {
        Block,
        TextNode
    },

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
        },
    },

    block: defineBlock({
        name: 'repeaters.faq',
        displayName: 'FAQ',
        icon: H1Icon,

        defaultContent: {
            title: 'Frequenly Asked Questions',
            width: 'standard',
            bottom_space: 'small',
            features: [
                {
                    content: {
                        heading: "What's the best thing about Switzerland?",
                        answer: "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                    },
                },
                {
                    content: {
                        heading: "What's the best thing about Switzerland?",
                        answer: "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                    },
                },
                {
                    content: {
                        heading: "What's the best thing about Switzerland?",
                        answer: "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                    },
                },
            ]
        },

        categories: [
            'contents'
        ],

        fields: [
            {
                name: 'title',
                label: 'Title',
                type: 'text',
            },
            {
                name: 'features',
                label: 'Features',
                type: 'repeater',
                fields: [
                    {
                        name: 'heading',
                        label: 'Question',
                        type: 'text',
                    },
                    {
                        name: 'answer',
                        label: 'Answer',
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
        ]
    })
}
</script>
