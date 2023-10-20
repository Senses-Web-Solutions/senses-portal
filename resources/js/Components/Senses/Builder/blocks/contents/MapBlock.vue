<template>
    <Block :class="{ 'mb-0': content.bottom_space == 'none', 'mb-10': content.bottom_space == 'small', 'mb-16': content.bottom_space == 'medium', 'mb-24': content.bottom_space == 'large' }">
        <img class="mx-auto" :src="`https://api.mapbox.com/styles/v1/mapbox/outdoors-v11/static/pin-s+555555(${content.longitude},${content.latitude})/${content.longitude},${content.latitude},13.75,0/${content.width === 'small' ? '960x330' : '1216x632'}?access_token=pk.eyJ1IjoibWF0dGhpc2NvY2siLCJhIjoiY2szOGo1OGx6MDk5NTNncGlxYjNkbHk1cCJ9.YJP7ged0QX3mMwY4m8ERwg`">
    </Block>
</template>
<script>

import { Block, BlockMixin, defineBlock, TextNode } from "@senses/builder";
import { defineComponent } from "vue";

import H1Icon from '../../icons/svg/map-block.svg?raw';

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
        name: 'contents.map-block',
        displayName: 'Map Block',
        icon: H1Icon,
        defaultContent: {
            paragraph: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Elementum sagittis vitae et leo duis ut.',
            longitude: '-0.142',
            latitude: '51.50',
            width: 'small',
            bottom_space: 'small',
        },
        categories: [
            'contents'
        ],
        fields: [
            {
                name: 'longitude',
                label: 'Longitude',
                type: 'text',
            },
            {
                name: 'latitude',
                label: 'Latitude',
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
                                label: 'Large',
                                value: 'large'
                            },
                            {
                                label: 'Small',
                                value: 'small'
                            },
                        ]
                    },
                ]
            }
        ]
    })
})
</script>
