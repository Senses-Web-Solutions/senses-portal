<template>
    <Block class=""
        :class="{ 'w-full': content.width === 'full', 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8': content.width === 'standard', 'max-w-5xl mx-auto px-4 sm:px-6 lg:px-8': content.width === 'reduced', 'mb-0': content.bottom_space == 'none', 'mb-10': content.bottom_space == 'small', 'mb-16': content.bottom_space == 'medium', 'mb-24': content.bottom_space == 'large' }">

        <div class="">
            <div class="w-full">
                <iframe class="mx-auto w-full lg:w-2/3" :width="content.frame_width" :height="content.frame_height" :src="'https://www.youtube.com/embed/' + content.videolink" title="YouTube video player" frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
            </div>
        </div>
    </Block>
</template>

<script lang="ts">

import { Block, BlockMixin, defineBlock, InternalDrop } from "@senses/builder";
import { defineComponent } from "vue";

import H1Icon from '../../icons/svg/video-block.svg?raw';

export default defineComponent({
    components: { Block },
    mixins: [BlockMixin],
    methods: {
        input(e) {
            const content = this.content;
            content.text = e.target.innerText;
            this.$emit('update:content', content);
        }
    },
    block: defineBlock({
        name: 'images.video-block',
        displayName: 'Video Block',
        icon: H1Icon,
        defaultContent: {
            focus: 'centre',
            image: 'https://designertravel.co.uk/storage/images/cypruspbay2/cypruspbay2_800.webp',
            shape: 'banner',
            frame_width: '512',
            frame_height: '400',
            alignment: 'centre',
            width: 'standard',
            bottom_space: 'small',
            videolink: '',
        },
        categories: [
            'images'
        ],
        fields: [
            {
                name: 'videolink',
                label: 'Video Link',
                type: 'text',
            },
            {
                name: 'section2',
                label: 'Layout',
                type: 'collapse',
                fields: [
                    {
                        name: 'frame_height',
                        label: 'Height',
                        type: 'text',
                    },
                    {
                        name: 'frame_width',
                        label: 'Width',
                        type: 'text',
                    },
                    {
                        name: 'alignment',
                        label: 'Alignment (Original Only)',
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
