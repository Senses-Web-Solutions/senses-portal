<template>
    <Block class=""
        :class="{ 'w-full': content.width === 'full', 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8': content.width === 'standard', 'max-w-5xl mx-auto px-4 sm:px-6 lg:px-8': content.width === 'reduced', 'mb-0': content.bottom_space == 'none', 'mb-10': content.bottom_space == 'small', 'mb-16': content.bottom_space == 'medium', 'mb-24': content.bottom_space == 'large' }">
        <div v-if="content.shape === 'banner'">
            <div class=" h-44">
                <img class="w-full h-full object-cover rounded-lg shadow-lg object-bottom" :class="{
                    'object-top': content.focus === 'top',
                    'object-center': content.focus === 'centre',
                    'object-bottom': content.focus === 'bottom'
                }" :src="content.image" alt="">

            </div>
        </div>
        <div class="" v-if="content.shape === 'original'">
            <div class="w-full">
                <img class="rounded-lg shadow-lg mx-auto" :class="{
                    'object-top': content.focus === 'top',
                    'object-center': content.focus === 'centre',
                    'object-bottom': content.focus === 'bottom'
                }" :src="content.image" alt="">
            </div>
        </div>
    </Block>
</template>

<script>

import { Block, BlockMixin, defineBlock, InternalDrop } from "@senses/builder";
import { defineComponent } from "vue";
import mapFunction from '../../builderFileMap';
import endpoint from '../../builderFileUrl';

import H1Icon from '../../icons/svg/shark/wide-image.svg?raw';

export default defineComponent({
    components: { Block },
    mixins: [BlockMixin],
    methods: {
        input(e) {
            const content = this.content;
            content.text = e.target.innerText;
            this.$emit('update:content', content)
        }
    },
    block: defineBlock({
        name: 'images.single-image',
        displayName: 'Single Image',
        icon: H1Icon,
        defaultContent: {
            focus: 'centre',
            image: 'https://designertravel.co.uk/storage/images/cypruspbay2/cypruspbay2_800.webp',
            shape: 'banner',
            alignment: 'centre',
            width: 'standard',
            bottom_space: 'small',
            url: '#'
        },
        categories: [
            'images'
        ],
        fields: [
            {
                name: 'image',
                label: 'Image',
                type: 'media-library',
                endpoint,
                mapFunction
            },
            {
                name: 'url',
                label: 'Url',
                type: 'text',
            },
            {
                name: 'section2',
                label: 'Layout',
                type: 'collapse',
                fields: [
                    {
                        name: 'shape',
                        label: 'Shape',
                        type: 'select',
                        options: [
                            {
                                label: 'Original',
                                value: 'original'
                            },
                            {
                                label: 'Banner',
                                value: 'banner',
                            },
                        ],
                    },

                    // {
                    //     name: 'alignment',
                    //     label: 'Alignment (Original Only)',
                    //     type: 'select',
                    //     options: [
                    //         {
                    //             label: 'Left',
                    //             value: 'left',
                    //         },
                    //         {
                    //             label: 'Centre',
                    //             value: 'centre',
                    //         },
                    //         {
                    //             label: 'Right',
                    //             value: 'right'
                    //         }
                    //     ]
                    // },
                    {
                        name: 'focus',
                        label: 'Focus',
                        type: 'select',
                        options: [
                            {
                                label: 'Top',
                                value: 'top',
                            },
                            {
                                label: 'Centre',
                                value: 'centre',
                            },
                            {
                                label: 'Bottom',
                                value: 'bottom'
                            }
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
        ]
    })
})
</script>
