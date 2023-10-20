<template>
    <Block class=""
        :class="{ 'w-full': content.width === 'full', 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8': content.width === 'standard', 'max-w-5xl mx-auto px-4 sm:px-6 lg:px-8': content.width === 'reduced', 'mb-0': content.bottom_space == 'none', 'mb-10': content.bottom_space == 'small', 'mb-16': content.bottom_space == 'medium', 'mb-24': content.bottom_space == 'large' }">
        <div class="">
            <div class="grid lg:grid-cols-6 md:grid-cols-3 grid-cols-2  gap-x-10 gap-y-10">
                <div v-for="feature in content.features" class=" col-span-1 lg:w-44 lg:h-44 xs:w-32 xs:h-32 object-cover transition duration-500 ">
                    <div v-if="feature.content.span === 'double'" class="col-span-1">
                        <img class="lg:w-44 lg:h-20 xs:w-32 xs:h-32 object-cover mb-4 transition duration-500 shadow-lg hover:shadow-2xl" :src="feature.content.image1">
                        <img class="lg:w-44 lg:h-20 xs:w-32 xs:h-32 object-cover transition duration-500 shadow-lg hover:shadow-2xl" :src="feature.content.image2">
                    </div>
                    <img v-if="feature.content.span === 'single'" class=" col-span-1 lg:w-44 lg:h-44 xs:w-32 xs:h-32 object-cover transition duration-500 shadow-lg hover:shadow-2xl" :src="feature.content.image1">
                </div>
            </div>
        </div>
    </Block>
</template>

<script lang="ts">

import { Block, BlockMixin, defineBlock, TextNode, } from "@senses/builder";
import { defineComponent } from "vue";

import H1Icon from '../../icons/svg/awards-gallery.svg?raw';

export default defineComponent({
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
    },

    block: defineBlock({
        name: 'repeaters.awards-gallery',
        displayName: 'Awards Gallery',
        icon: H1Icon,
        defaultContent: {
            width: 'standard',
            bottom_space: 'small',
            features: [
                {
                    content: {
                        heading: "Award 1",
                        span: 'single',
                        image1: 'https://images.unsplash.com/photo-1440778303588-435521a205bc?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=2070&q=80',
                        url: '#',
                    },
                },
                {
                    content: {
                        heading: "Award 2",
                        span: 'single',
                        image1: 'https://images.unsplash.com/photo-1440778303588-435521a205bc?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=2070&q=80',
                        url: '#',
                    },
                },
                {
                    content: {
                        heading: "Award 3",
                        span: 'single',
                        image1: 'https://images.unsplash.com/photo-1440778303588-435521a205bc?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=2070&q=80',
                        url: '#',
                    },
                },
                {
                    content: {
                        heading: "Award 4",
                        span: 'single',
                        image1: 'https://images.unsplash.com/photo-1440778303588-435521a205bc?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=2070&q=80',
                        url: '#',
                    },
                },
                {
                    content: {
                        heading: "Award 5",
                        span: 'single',
                        image1: 'https://images.unsplash.com/photo-1440778303588-435521a205bc?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=2070&q=80',
                        url: '#',
                    },
                },
                {
                    content: {
                        heading: "Award 6",
                        span: 'single',
                        image1: 'https://images.unsplash.com/photo-1440778303588-435521a205bc?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=2070&q=80',
                        url: '#',
                    },
                },
            ]
        },

        categories: [
            'images'
        ],

        fields: [
            {
                name: 'features',
                label: 'Features',
                type: 'repeater',
                fields: [
                    {
                        name: 'image1',
                        label: 'Image 1',
                        type: 'award',
                    },
                    {
                        name: 'image2',
                        label: 'Image 2 (will only show if span set to double)',
                        type: 'award',
                    },
                    {
                        name: 'url',
                        label: 'Url',
                        type: 'text',
                    },
                    {
                        name: 'span',
                        label: 'Span',
                        type: 'select',
                        options: [
                            {
                                label: 'Single',
                                value: 'single',
                            },
                            {
                                label: 'Double',
                                value: 'double',
                            },
                        ]
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
