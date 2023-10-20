<template>
    <Block
        :class="{ 'w-full': content.width === 'full', 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8': content.width === 'standard', 'max-w-5xl mx-auto px-4 sm:px-6 lg:px-8': content.width === 'reduced', 'mb-0': content.bottom_space == 'none', 'mb-10': content.bottom_space == 'small', 'mb-16': content.bottom_space == 'medium', 'mb-24': content.bottom_space == 'large' }">
        <div class="">
            <div class="mx-auto max-w-2xl sm:text-center">
                <h2 class="text-3xl font-medium font-serif tracking-tight text-zinc-700 sm:text-4xl">{{ content.title }}</h2>
                <p class="mt-2 text-lg leading-8 text-zinc-600">{{ content.subtitle }}</p>
            </div>
            <div v-for="feature in content?.features" class="mx-auto mt-20 space-y-16">
                <div class="grid grid-cols-12">
                    <div class="col-span-1">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-indigo-600">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" :d="feature.content.svg" />
                            </svg>
                        </div>
                    </div>
                    <div class="col-span-11">
                        <h3 class="text-base font-semibold leading-7 text-zinc-900 font-serif">{{ feature.content.heading }}</h3>
                        <p class="mt-2 leading-7 text-zinc-600">{{ feature.content.text }}</p>
                        <p class="mt-4">
                        <div class="text-sm font-semibold leading-6 text-indigo-600 ">{{ feature.content.link_text }} <span aria-hidden="true">&rarr;</span></div>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </Block>
</template>
<script lang="ts">

import { Block, BlockMixin, defineBlock, TextNode } from "@senses/builder";

import H1Icon from '../../icons/svg/full-width-list-with-title.svg?raw';

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
        name: 'repeaters.full-width-list',
        displayName: 'Full Width List',
        icon: H1Icon,

        defaultContent: {
            title: 'Title',
            subtitle: 'Subtitle goes here',
            width: 'standard',
            bottom_space: 'small',
            features: [
                {
                    content: {
                        heading: "Block 1",
                        text: "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                        url: "#",
                        link_text: 'Link Text',
                        svg: 'M9 8.25H7.5a2.25 2.25 0 00-2.25 2.25v9a2.25 2.25 0 002.25 2.25h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25H15M9 12l3 3m0 0l3-3m-3 3V2.25',
                    },
                },
                {
                    content: {
                        heading: "Block 2",
                        text: "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                        url: "#",
                        link_text: 'Link Text',
                        svg: 'M9 8.25H7.5a2.25 2.25 0 00-2.25 2.25v9a2.25 2.25 0 002.25 2.25h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25H15M9 12l3 3m0 0l3-3m-3 3V2.25',
                    },
                },
                {
                    content: {
                        heading: "Block 3",
                        text: "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                        url: "#",
                        link_text: 'Link Text',
                        svg: 'M9 8.25H7.5a2.25 2.25 0 00-2.25 2.25v9a2.25 2.25 0 002.25 2.25h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25H15M9 12l3 3m0 0l3-3m-3 3V2.25',
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
                name: 'subtitle',
                label: 'Subtitle',
                type: 'text',
            },
            {
                name: 'features',
                label: 'Features',
                type: 'repeater',
                fields: [
                    {
                        name: 'svg',
                        label: 'Icon',
                        type: 'svg',
                    },
                    {
                        name: 'heading',
                        label: 'Heading',
                        type: 'text',
                    },
                    {
                        name: 'text',
                        label: 'Text',
                        type: 'textarea',
                    },
                    {
                        name: 'link_text',
                        label: 'Link Text',
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
        ]
    })
}
</script>
