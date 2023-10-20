<template>
    <Block  :class="{'mb-0' : content.bottom_space == 'none', 'mb-10' : content.bottom_space == 'small', 'mb-16' : content.bottom_space == 'medium', 'mb-24' : content.bottom_space == 'large'}">
        <div class="max-w-7xl mx-auto">
            <div class="max-w-lg mx-auto grid gap-5 lg:grid-cols-3 lg:max-w-none px-8 xl:px-0">
                <!--card one-->
                <div v-for="feature in content?.features" class="flex flex-col overflow-hidden text-center">
                    <div class="flex-1 flex flex-col justify-between">
                        <div class="flex-1">
                            <div class="block">
                                <p class="text-xl font-medium text-zinc-700 font-serif">
                                    {{feature.content.heading}}
                                </p>
                                <ul class="mt-4 space-y-4">
                                    <li>
                                        <p class="text-base text-zinc-500 hover:text-zinc-900">
                                            {{feature.content.line1}}
                                        </p>
                                    </li>
                                    <li>
                                        <p class="text-base text-zinc-500 hover:text-zinc-900">
                                            {{feature.content.line2}}
                                        </p>
                                    </li>
                                    <li>
                                        <p class="text-base text-zinc-500 hover:text-zinc-900">
                                            {{feature.content.line3}}
                                        </p>
                                    </li>
                                    <li>
                                        <p class="text-base text-zinc-500 hover:text-zinc-900">
                                            {{feature.content.line4}}
                                        </p>
                                    </li>
                                    <li>
                                        <p class="text-base text-zinc-500 hover:text-zinc-900">
                                            {{feature.content.line5}}
                                        </p>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Block>
</template>
<script lang="ts">

import { Block, BlockMixin, defineBlock, TextNode, InternalDrop } from "@senses/builder";
import { defineComponent } from "vue";

import H1Icon from '../../icons/svg/text-category-3-cols.svg?raw';

export default defineComponent({
    components: {
        Block,
        TextNode
    },

    mixins: [BlockMixin],

    methods: {
        input(e) {
            const content = this.content;
            content.heading = e.target.innerText;
            this.$emit('update:content', content)
        }
    },

    block: defineBlock({
        name: 'contents.column-text',
        displayName: 'Column Text',
        icon: H1Icon,

        defaultContent: {
            bottom_space: 'small',
            features: [
                {
                    content: {
                        heading: "Experience & Expertise",
                        line1: "One of the UK's leading travel specialists",
                        line2: "Over 15 years of customer satisfaction",
                        line3: "Travel Experts to unique destinations",
                        line4: "Offices and shops in the UK",
                        line5: "Unique insight"
                    },
                },
                {
                    content: {
                        heading: "Peace of Mind",
                        line1: "ABTA Bonded",
                        line2: "24/7 customer care",
                        line3: "Fully escorted holidays",
                        line4: "So much included in the price",
                        line5: "Customer service included"
                    },
                },
                {
                    content: {
                        heading: "Helping Make it Memorable",
                        line1: "Connect with 40 more than suppliers",
                        line2: "Tailor made from start to finish",
                        line3: "Dedicated holiday experts",
                        line4: "Award winning staff",
                        line5: "The very best holiday prices"
                    },
                },
            ]
        },

        categories: [
            'contents'
        ],

        fields: [
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
                        name: 'line1',
                        label: 'Line 1',
                        type: 'text',
                    },
                    {
                        name: 'line2',
                        label: 'Line 2',
                        type: 'text',
                    },
                    {
                        name: 'line3',
                        label: 'Line 3',
                        type: 'text',
                    },
                    {
                        name: 'line4',
                        label: 'Line 4',
                        type: 'text',
                    },
                    {
                        name: 'line5',
                        label: 'Line 5',
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

                ]
            }
        ],
    }),
})
</script>
