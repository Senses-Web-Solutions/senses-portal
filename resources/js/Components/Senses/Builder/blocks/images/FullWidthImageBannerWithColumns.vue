<template>
    <Block class="relative"
        :class="{ 'mb-0': content.bottom_space == 'none', 'mb-10': content.bottom_space == 'small', 'mb-16': content.bottom_space == 'medium', 'mb-24': content.bottom_space == 'large' }">
        <div class="max-w-screen">
            <div class="relative sm:overflow-hidden">
                <div class="absolute inset-0 overflow-hidden">
                    <video v-if="extension === 'mp4'" class="w-full h-full object-cover"  playsinline>
                        <source :src="content.image" type="video/mp4">
                    </video>
                    <img v-else class="w-full h-full object-cover" :src="content.image" alt="">

                    <!-- <div class="absolute inset-0 bg-zinc-900 bg-opacity-50 mix-blend-multiply"></div> -->
                </div>
                <div class="relative px-4 py-16 sm:px-6 sm:py-24 lg:py-32 lg:px-8 max-w-7xl mx-auto">
                    <div class="grid grid-cols-3 gap-8">
                        <div class="col-span-1 bg-white rounded-2xl p-8">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" :viewBox="content.col_1_icon.viewBox" stroke-width="1.5" stroke="currentColor" class="w-7 h-7 mx-auto text-indigo-600 mb-3">
                                <path stroke-linecap="round" stroke-linejoin="round" :d="content.col_1_icon.path" />
                            </svg>
                            <p class="text-center text-2xl font-medium font-serif text-zinc-700 mb-3">
                                {{ content.col_1_title }}
                            </p>

                            <p class="text-base text-zinc-700 leading-relaxed text-center">
                                {{ content.col_1_text }}
                            </p>
                        </div>

                        <div class="col-span-1 bg-white rounded-2xl p-8">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" :viewBox="content.col_2_icon.viewBox" stroke-width="1.5" stroke="currentColor" class="w-7 h-7 mx-auto text-indigo-600 mb-3">
                                <path stroke-linecap="round" stroke-linejoin="round" :d="content.col_2_icon.path" />
                            </svg>

                            <p class="text-center text-2xl font-medium font-serif text-zinc-700 mb-3">
                                {{ content.col_2_title }}
                            </p>

                            <p class="text-base text-zinc-700 leading-relaxed text-center">
                                {{ content.col_2_text }}
                            </p>
                        </div>

                        <div class="col-span-1 bg-white rounded-2xl p-8">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" :viewBox="content.col_3_icon.viewBox" stroke-width="1.5" stroke="currentColor" class="w-7 h-7 mx-auto text-indigo-600 mb-3">
                                <path stroke-linecap="round" stroke-linejoin="round" :d="content.col_3_icon.path" />
                            </svg>
                            <p class="text-center text-2xl font-medium font-serif text-zinc-700 mb-3">
                                {{ content.col_3_title }}
                            </p>

                            <p class="text-base text-zinc-700 leading-relaxed text-center">
                                {{ content.col_3_text }}
                            </p>
                        </div>

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
import mapFunction from '../../builderFileMap';
import endpoint from '../../builderFileUrl';

export default defineComponent({
    components: { Block, TextNode },
    mixins: [BlockMixin],

    computed: {
        extension() {
            return this.content.image.split('.').pop();
        }
    },

    mounted() {
        this.getLastestBlogs();
    },
    methods: {
        input(e) {
            const content = this.content;
            content.text = e.target.innerText;
            this.$emit('update:content', content)
        },

        getLastestBlogs() {
            axios.get('/api/retrieve-latest-blogs').then(response => {
                console.log(response);
            })
        }
    },
    block: defineBlock({
        name: "images.full-width-image-banner-with-columns",
        displayName: "Full Width Banner With Columns",
        categories: ["images"],
        icon: Icon,
        defaultContent: {
            image: "https://designertravel.co.uk/storage/images/splitcroatiapbay/splitcroatiapbay_1200.webp",
            col_1_title: 'Column 1',
            col_1_text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            col_1_icon: {viewBox: '0 0 640 512', path: "M443.48 18.08C409.77 5.81 375.31 0 341.41 0c-90.47 0-176.84 41.45-233.44 112.33-6.7 8.39-2.67 21.04 7.42 24.71l236.15 85.95L257.99 480H8c-4.42 0-8 3.58-8 8v16c0 4.42 3.58 8 8 8h560c4.42 0 8-3.58 8-8v-16c0-4.42-3.58-8-8-8H292.03l89.56-246.07 236.75 86.17c1.83.67 3.7.98 5.53.98 8.27 0 15.82-6.35 16.04-15.14 3.03-124.66-72.77-242.85-196.43-287.86zm-295.31 96.84C198.11 62.64 268.77 32 341.42 32c7.81 0 15.6.35 23.36 1.04-36.87 23.16-73.76 66.62-103.06 123.21l-113.55-41.33zm315.21 114.73l-171.12-62.28C332.69 90.93 384.89 46.1 420.4 46.09c4.35 0 8.32.68 12.13 2.06 19.56 7.12 33.97 35.16 38.56 75 3.66 31.83.53 68.45-7.71 106.5zm30.8 11.21c13.83-61.57 13.67-118.28.7-159.64 65.33 46.08 107.58 119.45 112.61 200.89l-113.31-41.25z"},
            col_2_title: 'Column 2',
            col_2_text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            col_2_icon: {viewBox: '0 0 640 512', path: "M443.48 18.08C409.77 5.81 375.31 0 341.41 0c-90.47 0-176.84 41.45-233.44 112.33-6.7 8.39-2.67 21.04 7.42 24.71l236.15 85.95L257.99 480H8c-4.42 0-8 3.58-8 8v16c0 4.42 3.58 8 8 8h560c4.42 0 8-3.58 8-8v-16c0-4.42-3.58-8-8-8H292.03l89.56-246.07 236.75 86.17c1.83.67 3.7.98 5.53.98 8.27 0 15.82-6.35 16.04-15.14 3.03-124.66-72.77-242.85-196.43-287.86zm-295.31 96.84C198.11 62.64 268.77 32 341.42 32c7.81 0 15.6.35 23.36 1.04-36.87 23.16-73.76 66.62-103.06 123.21l-113.55-41.33zm315.21 114.73l-171.12-62.28C332.69 90.93 384.89 46.1 420.4 46.09c4.35 0 8.32.68 12.13 2.06 19.56 7.12 33.97 35.16 38.56 75 3.66 31.83.53 68.45-7.71 106.5zm30.8 11.21c13.83-61.57 13.67-118.28.7-159.64 65.33 46.08 107.58 119.45 112.61 200.89l-113.31-41.25z"},
            col_3_title: 'Column 3',
            col_3_text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            col_3_icon: {viewBox: '0 0 640 512', path: "M443.48 18.08C409.77 5.81 375.31 0 341.41 0c-90.47 0-176.84 41.45-233.44 112.33-6.7 8.39-2.67 21.04 7.42 24.71l236.15 85.95L257.99 480H8c-4.42 0-8 3.58-8 8v16c0 4.42 3.58 8 8 8h560c4.42 0 8-3.58 8-8v-16c0-4.42-3.58-8-8-8H292.03l89.56-246.07 236.75 86.17c1.83.67 3.7.98 5.53.98 8.27 0 15.82-6.35 16.04-15.14 3.03-124.66-72.77-242.85-196.43-287.86zm-295.31 96.84C198.11 62.64 268.77 32 341.42 32c7.81 0 15.6.35 23.36 1.04-36.87 23.16-73.76 66.62-103.06 123.21l-113.55-41.33zm315.21 114.73l-171.12-62.28C332.69 90.93 384.89 46.1 420.4 46.09c4.35 0 8.32.68 12.13 2.06 19.56 7.12 33.97 35.16 38.56 75 3.66 31.83.53 68.45-7.71 106.5zm30.8 11.21c13.83-61.57 13.67-118.28.7-159.64 65.33 46.08 107.58 119.45 112.61 200.89l-113.31-41.25z"},
            bottom_space: 'small'
        },
        fields: [
            {
                name: 'image',
                label: 'Select Image',
                type: 'media-library',
                endpoint,
                mapFunction,
            },
            {
                name: 'column1',
                label: 'Column 1',
                type: 'collapse',
                fields: [
                    {
                        name: 'col_1_title',
                        label: 'Title',
                        type: 'text',
                    },
                    {
                        name: 'col_1_text',
                        label: 'Text',
                        type: 'text',
                    },
                    {
                        name: 'col_1_icon',
                        label: 'icon',
                        type: 'svg',
                    },
                ],
            },
            {
                name: 'column2',
                label: 'Column 2',
                type: 'collapse',
                fields: [
                    {
                        name: 'col_2_title',
                        label: 'Title',
                        type: 'text',
                    },
                    {
                        name: 'col_2_text',
                        label: 'Text',
                        type: 'text',
                    },
                    {
                        name: 'col_2_icon',
                        label: 'icon',
                        type: 'svg',
                    },
                ],
            },
            {
                name: 'column3',
                label: 'Column 3',
                type: 'collapse',
                fields: [
                    {
                        name: 'col_3_title',
                        label: 'Title',
                        type: 'text',
                    },
                    {
                        name: 'col_3_text',
                        label: 'Text',
                        type: 'text',
                    },
                    {
                        name: 'col_3_icon',
                        label: 'icon',
                        type: 'svg',
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
                    }
                ]
            }
        ],
    }),
});
</script>
