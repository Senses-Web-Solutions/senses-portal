<template>
    <Block class="mx-auto"
        :class="{ 'mb-0': content.bottom_space === 'none', 'mb-10': content.bottom_space === 'small', 'mb-16': content.bottom_space === 'medium', 'mb-24': content.bottom_space === 'large' }">

        <div full-width class="">
            <div class="h-96 relative">
                <video v-if="extension === 'mp4'" class="w-full h-full object-cover" autoplay muted loop playsinline>
                    <source :src="content.image" type="video/mp4">
                </video>
                <img v-else class="w-full h-full object-cover" :src="content.image" alt="">
                <div class="absolute inset-0 bg-zinc-400 h-96" style="mix-blend-mode: multiply;" aria-hidden="true"></div>
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
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex my-8 justify-start" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-4">
                    <li>
                        <div>
                            <p class="text-sm font-medium text-zinc-500 hover:text-zinc-700" aria-current="page">
                                Home
                            </p>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <!-- Heroicon name: solid/chevron-right -->
                            <svg class="flex-shrink-0 h-5 w-5 text-zinc-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <p class="ml-4 text-sm font-medium text-zinc-500 hover:text-zinc-700 overflow-hidden whitespace-nowrap overflow_ellipsis">{{ content.title }}</p>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    </Block>
</template>
<script lang="ts">
import { defineComponent } from 'vue';
import { Block, BlockMixin, defineBlock, TextNode, InternalDrop } from "@senses/builder";

import H1Icon from '../../icons/svg/shark/hero-banner.svg?raw';
import mapFunction from '../../builderFileMap';
import endpoint from '../../builderFileUrl';

export default defineComponent({
    components: { Block, TextNode },

    mixins: [BlockMixin],

    // mounted() {
        // this.content.title = 'Example Destination';
        // this.content.subtitle = 'Message any one of our seasoned travel experts to help you find your perfect holiday';
        // console.log(this.content);
    // },

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
        showMediaGallery() {
        }
    },

    block: defineBlock({
        name: 'headings.full-width-image-header',
        displayName: 'Full Width Banner',
        fullWidth: true,
        icon: H1Icon,
        defaultContent: {
            subtitle: "Put your subtitle here",
            title: "Example Page",
            image: "https://designertravel.co.uk/storage/images/splitcroatiapbay/splitcroatiapbay_1200.webp"
        },
        categories: [
            'headings'
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
