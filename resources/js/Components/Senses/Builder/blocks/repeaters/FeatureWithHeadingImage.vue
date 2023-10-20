
<template>
    <Block class="relative"
        :class="{ 'mb-0': content.bottom_space == 'none', 'mb-10': content.bottom_space == 'small', 'mb-16': content.bottom_space == 'medium', 'mb-24': content.bottom_space == 'large' }">
        <div class="max-w-screen">
            <div class="z-0 h-96">
                <div class="inset-0 overflow-hidden bg-zinc-400 h-full z-0 relative">
                    <video v-if="extension === 'mp4' || extension === 'm4v'" class="w-full h-full object-cover" autoplay muted loop playsinline>
                        <source :src="content.image" :type="`video/${extension === 'm4v' ? 'x-m4v' : extension}`">
                    </video>
                    <img v-else class="w-full h-full object-cover" :src="content.image" alt="">

                    <div class="absolute inset-0 bg-zinc-400 bg-opacity-50 mix-blend-multiply"></div>

                    <div class="absolute inset-0">
                    <div class="flex h-full items-center max-w-7xl mx-auto px-4 m-auto sm:px-6 lg:px-8">
                        <div class="mx-auto max-w-3xl text-center">
                            <!-- <div class="bg-white rounded-2xl p-8 shadow-lg mx-auto"> -->
                                <TextNode v-model="content.title" class="text-4xl font-medium text-white sm:text-5xl md:text-6xl lg:text-5xl xl:text-6xl font-serif mb-6" basic>
                                </TextNode>
                                <TextNode v-model="content.subtitle" class="mt-3 max-w-md mx-auto text-xl text-white sm:text-xl md:mt-5 md:max-w-3xl" basic></TextNode>
                            <!-- </div> -->
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
        <div class="-mt-20 max-w-7xl mx-auto px-8 z-40">
            <div class="grid grid-cols-3 gap-8 gap-y-16 ">
                <div v-for="feature in content?.features">
                    <div class="bg-white rounded-2xl shadow-lg relative hover:shadow-2xl z-40 flex flex-col justify-between">
                        <div class="flex-1 relative pt-16 px-5 pb-8 md:px-8 relative z-40">
                            <div class="absolute top-0 p-5 inline-block rounded-xl shadow-lg transform -translate-y-1/2  z-40" :class="'bg-' + feature.content.icon_bg_colour">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" :viewBox="feature.content.svg.viewBox" stroke-width="1.5" stroke="currentColor" class="w-7 h-7"
                                    :class="'text-' + feature.content.icon_colour">
                                    <path stroke-linecap="round" stroke-linejoin="round" :d="feature.content.svg.path" />
                                </svg>
                            </div>
                            <h3 class="text-2xl font-medium text-zinc-700 font-serif break-words">{{ feature.content.heading ?? 'Your Heading' }}</h3>
                            <p v-html="nl2br(feature.content.paragraph, true)" class="mt-4 text-base font-normal text-zinc-500 overflow-hidden" />
                        </div>
                        <div class="p-6 bg-zinc-50 rounded-bl-2xl rounded-br-2xl md:px-8">
                            <div class="text-base font-medium text-indigo-700 hover:text-indigo-600 capitalize">
                                {{ feature.content.button_text ?? 'Button Text' }}<span aria-hidden="true"> →</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Block>
</template>

<script lang="ts">

import { Block, BlockMixin, defineBlock, TextNode } from "@senses/builder";

import H1Icon from '../../icons/svg/shark/grid-3-image-no-rating.svg?raw';
import { defineComponent } from "vue";
import mapFunction from '../../builderFileMap';
import endpoint from '../../builderFileUrl';

export default defineComponent({
    components: {
        Block,
        TextNode
    },

    mixins: [BlockMixin],

    computed: {
        extension() {
            return this.content.image.split('.').pop();
        }
    },

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
        name: 'repeaters.features-with-heading',
        displayName: 'Features Grid With Heading',
        icon: H1Icon,

        defaultContent: {
            width: 'standard',
            bottom_space: 'small',
            image: 'https://images.unsplash.com/photo-1440778303588-435521a205bc?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=2070&q=80',
            subtitle: "Put your subtitle here",
            title: "Example Page",
            features: [
                {
                    content: {
                        heading: "Block 1",
                        paragraph: "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                        button_text: "Read More",
                        icon_bg_colour: 'indigo-900',
                        icon_colour: 'white',
                        button_url: "#",
                        open_live_chat: false,
                        svg: {viewBox: '0 0 640 512',path: "M443.48 18.08C409.77 5.81 375.31 0 341.41 0c-90.47 0-176.84 41.45-233.44 112.33-6.7 8.39-2.67 21.04 7.42 24.71l236.15 85.95L257.99 480H8c-4.42 0-8 3.58-8 8v16c0 4.42 3.58 8 8 8h560c4.42 0 8-3.58 8-8v-16c0-4.42-3.58-8-8-8H292.03l89.56-246.07 236.75 86.17c1.83.67 3.7.98 5.53.98 8.27 0 15.82-6.35 16.04-15.14 3.03-124.66-72.77-242.85-196.43-287.86zm-295.31 96.84C198.11 62.64 268.77 32 341.42 32c7.81 0 15.6.35 23.36 1.04-36.87 23.16-73.76 66.62-103.06 123.21l-113.55-41.33zm315.21 114.73l-171.12-62.28C332.69 90.93 384.89 46.1 420.4 46.09c4.35 0 8.32.68 12.13 2.06 19.56 7.12 33.97 35.16 38.56 75 3.66 31.83.53 68.45-7.71 106.5zm30.8 11.21c13.83-61.57 13.67-118.28.7-159.64 65.33 46.08 107.58 119.45 112.61 200.89l-113.31-41.25z"},
                    },
                },
                {
                    content: {
                        heading: "Block 2",
                        paragraph: "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                        button_text: "Read More",
                        icon_bg_colour: 'indigo-900',
                        icon_colour: 'white',
                        button_url: "#",
                        open_live_chat: false,
                        svg: {viewBox: '0 0 640 512',path: "M443.48 18.08C409.77 5.81 375.31 0 341.41 0c-90.47 0-176.84 41.45-233.44 112.33-6.7 8.39-2.67 21.04 7.42 24.71l236.15 85.95L257.99 480H8c-4.42 0-8 3.58-8 8v16c0 4.42 3.58 8 8 8h560c4.42 0 8-3.58 8-8v-16c0-4.42-3.58-8-8-8H292.03l89.56-246.07 236.75 86.17c1.83.67 3.7.98 5.53.98 8.27 0 15.82-6.35 16.04-15.14 3.03-124.66-72.77-242.85-196.43-287.86zm-295.31 96.84C198.11 62.64 268.77 32 341.42 32c7.81 0 15.6.35 23.36 1.04-36.87 23.16-73.76 66.62-103.06 123.21l-113.55-41.33zm315.21 114.73l-171.12-62.28C332.69 90.93 384.89 46.1 420.4 46.09c4.35 0 8.32.68 12.13 2.06 19.56 7.12 33.97 35.16 38.56 75 3.66 31.83.53 68.45-7.71 106.5zm30.8 11.21c13.83-61.57 13.67-118.28.7-159.64 65.33 46.08 107.58 119.45 112.61 200.89l-113.31-41.25z"},
                    },
                },
                {
                    content: {
                        heading: "Block 3",
                        paragraph: "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                        button_text: "Read More",
                        icon_bg_colour: 'indigo-900',
                        icon_colour: 'white',
                        button_url: "#",
                        open_live_chat: false,
                        svg: {viewBox: '0 0 640 512',path: "M443.48 18.08C409.77 5.81 375.31 0 341.41 0c-90.47 0-176.84 41.45-233.44 112.33-6.7 8.39-2.67 21.04 7.42 24.71l236.15 85.95L257.99 480H8c-4.42 0-8 3.58-8 8v16c0 4.42 3.58 8 8 8h560c4.42 0 8-3.58 8-8v-16c0-4.42-3.58-8-8-8H292.03l89.56-246.07 236.75 86.17c1.83.67 3.7.98 5.53.98 8.27 0 15.82-6.35 16.04-15.14 3.03-124.66-72.77-242.85-196.43-287.86zm-295.31 96.84C198.11 62.64 268.77 32 341.42 32c7.81 0 15.6.35 23.36 1.04-36.87 23.16-73.76 66.62-103.06 123.21l-113.55-41.33zm315.21 114.73l-171.12-62.28C332.69 90.93 384.89 46.1 420.4 46.09c4.35 0 8.32.68 12.13 2.06 19.56 7.12 33.97 35.16 38.56 75 3.66 31.83.53 68.45-7.71 106.5zm30.8 11.21c13.83-61.57 13.67-118.28.7-159.64 65.33 46.08 107.58 119.45 112.61 200.89l-113.31-41.25z"},
                    },
                },
            ]
        },

        categories: [
            'contents'
        ],

        fields: [
            {
                name: 'image',
                label: 'Select Image',
                type: 'media-library',
                endpoint,
                mapFunction,
            },
            {
                name: 'section-1',
                label: 'Content',
                type: 'collapse',
                fields: [
                    {
                        name: 'title',
                        label: 'Title',
                        type: 'text',
                    },
                    {
                        name: 'subtitle',
                        label: 'Subtitle',
                        type: 'textarea',
                    },
                ]
            },
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
                        name: 'svg',
                        label: 'Icon',
                        type: 'svg',
                    },
                    {
                        name: 'paragraph',
                        label: 'Paragraph',
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
                        name: 'open_live_chat',
                        label: 'Open Live Chat',
                        type: 'toggle',
                    },
                    {
                        name: 'colours',
                        label: 'Colours',
                        type: 'collapse',
                        fields: [
                            {
                                name: 'icon_colour',
                                label: 'Icon Colour',
                                type: 'colour',
                            },
                            {
                                name: 'icon_bg_colour',
                                label: 'Icon Background Colour',
                                type: 'colour',
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
                    }
                ]
            }
        ]
    })
})
</script>
