<template>
    <Block class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" :class="{'mb-0' : content.bottom_space == 'none', 'mb-10' : content.bottom_space == 'small', 'mb-16' : content.bottom_space == 'medium', 'mb-24' : content.bottom_space == 'large'}">
        <div class="max-w-3xl mx-auto lg:max-w-7xl px-4 sm:px-6 lg:px-8" :class="{
            'text-left': content.alignment === 'left',
            'text-center': content.alignment === 'centre',
            'text-right': content.alignment === 'right'
        }">
            <TextNode basic
                class="pointer inline-block px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-black hover:bg-indigo-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
                placeholder="Enter Text" :model-value="content.text" @update:modelValue="update('text', $event)"></TextNode>
        </div>
    </Block>
</template>
<script>
import { defineComponent } from 'vue';
import { Block, BlockMixin, defineBlock, TextNode, InternalDrop } from "@senses/builder";

import Icon from '../../icons/svg/buttons/buttons-dark.svg?raw';

export default defineComponent({
    components: { Block, TextNode },
    mixins: [BlockMixin],
    methods: {
        input(e) {
            const content = this.content;
            content.text = e.target.innerText;
            this.$emit('update:content', content)
        }
    },
    block: defineBlock({
        name: "buttons.dark-button",
        displayName: "Dark Button",
        categories: ["buttons"],
        icon: Icon,
        defaultContent: {
            text: "Dark Button",
            bottom_space: 'small',
            url: '#',
            alignment: "centre",
        },
        fields: [
            {
                name: "text",
                label: "Text",
                type: "text",
            },
            {
                name: "url",
                label: "URL",
                type: "text",
            },
            {
                name: "alignment",
                label: "Alignment",
                type: "select",
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
                ]
            }
        ],
    }),
});
</script>
