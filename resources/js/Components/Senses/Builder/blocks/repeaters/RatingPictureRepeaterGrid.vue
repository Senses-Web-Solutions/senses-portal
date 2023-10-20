<template>
    <Block
        class=""
        :class="{
            'w-full': content.width === 'full',
            'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8':
                content.width === 'standard',
            'max-w-5xl mx-auto px-4 sm:px-6 lg:px-8':
                content.width === 'reduced',
            'mb-0': content.bottom_space == 'none',
            'mb-10': content.bottom_space == 'small',
            'mb-16': content.bottom_space == 'medium',
            'mb-24': content.bottom_space == 'large',
        }"
    >
        <div class="">
            <div class="grid grid-cols-3 gap-8 gap-y-16">
                <div v-for="feature in content?.features">
                    <div class="cursor-pointer">
                        <div
                            class="transition duration-500 flex flex-col rounded-lg shadow-lg overflow-hidden hover:shadow-2xl relative"
                            style="max-height: 610px; min-height: 610px"
                        >
                            <div
                                class="transition duration-700 ease-in-out flex-shrink-0 overflow-hidden relative"
                            >
                                <div
                                    v-if="feature.content.label"
                                    class="absolute top-5 right-0"
                                >
                                    <div
                                        :class="`bg-${feature.content.label_colour} px-3 py-2 rounded-l-lg`"
                                    >
                                        <p class="text-white font-semibold">
                                            {{ feature.content.label }}
                                        </p>
                                    </div>
                                </div>
                                <img
                                    class="h-60 w-full object-cover image-roll"
                                    :src="feature.content.image"
                                    alt=""
                                    loading="lazy"
                                />
                            </div>
                            <div
                                class="flex-1 bg-white p-6 flex flex-col justify-between min-h-48"
                            >
                                <div class="flex-1 items-center">
                                    <div
                                        class="text-base text-zinc-500 inline-flex space-between items-center"
                                    >
                                        <div
                                            class="flex-shrink-0 flex mb-3 space-x-2"
                                        >
                                            <div
                                                v-for="rating in parseInt(
                                                    feature.content.rating
                                                )"
                                                class="text-yellow-500"
                                                aria-hidden="true"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="h-5 w-5"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1
                            0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364
                            1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175
                            0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0
                            00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0
                            00.951-.69l1.07-3.292z"
                                                    ></path>
                                                </svg>
                                            </div>
                                            <div
                                                class="text-yellow-500 opacity-0"
                                                aria-hidden="true"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="h-5 w-5"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1
                            0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364
                            1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175
                            0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0
                            00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0
                            00.951-.69l1.07-3.292z"
                                                    ></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-xs text-zinc-400 flex">
                                        <p class="mb-1 underline">
                                            {{ feature.content.location }}
                                        </p>
                                    </div>
                                    <div class="h-16 max-h-16 overflow-hidden">
                                        <p
                                            class="text-2xl font-medium text-zinc-700 font-serif break-words"
                                        >
                                            {{ feature.content.heading }}
                                        </p>
                                    </div>

                                    <p
                                        v-html="
                                            nl2br(
                                                feature.content.paragraph,
                                                true
                                            )
                                        "
                                        class="mt-4 text-base text-zinc-500 overflow-hidden"
                                        style="height: 150px"
                                    />
                                </div>
                            </div>
                            <div
                                v-if="feature.content.url"
                                class="p-6 bg-zinc-50 rounded-bl-lg rounded-br-lg md:px-8 mt-2 absolute"
                                style="bottom: 0; right: 0; left: 0"
                            >
                                <div
                                    class="text-base font-medium text-indigo-900 hover:text-indigo-600 capitalize"
                                >
                                    {{ feature.content.button_text }}
                                    <span aria-hidden="true"> →</span>
                                </div>
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

import H1Icon from "../../icons/svg/shark/grid-3-ratings.svg?raw";
import mapFunction from '../../builderFileMap';
import endpoint from '../../builderFileUrl';

export default {
    components: {
        Block,
        TextNode,
    },

    mixins: [BlockMixin],

    methods: {
        input(e) {
            const content = this.content;
            content.text = e.target.innerText;
            this.$emit("update:content", content);
        },
        nl2br(str, is_xhtml) {
            if (typeof str === "undefined" || str === null) {
                return "";
            }
            var breakTag =
                is_xhtml || typeof is_xhtml === "undefined" ? "<br />" : "<br>";
            return (str + "").replace(
                /([^>\r\n]?)(\r\n|\n\r|\r|\n)/g,
                "$1" + breakTag + "$2"
            );
        },
    },

    block: defineBlock({
        name: "repeaters.rating-picture-repeater",
        displayName: "Rating & Picture Block",
        icon: H1Icon,

        defaultContent: {
            location: "Location",
            width: "standard",
            bottom_space: "small",
            features: [
                {
                    content: {
                        heading: "Block 1",
                        paragraph:
                            "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                        url: "#",
                        image: "https://designertravel.co.uk/storage/images/3456477167701739313157894951831739204864723n/3456477167701739313157894951831739204864723n_400.webp",
                        rating: 4,
                        button_text: "Button Text",
                        label: "",
                        label_colour: "",
                    },
                },
                {
                    content: {
                        heading: "Block 2",
                        paragraph:
                            "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                        url: "#",
                        image: "https://designertravel.co.uk/storage/images/3456477167701739313157894951831739204864723n/3456477167701739313157894951831739204864723n_400.webp",
                        rating: 4,
                        button_text: "Button Text",
                        label: "Exclusive Offer",
                        label_colour: "blue-500",
                    },
                },
                {
                    content: {
                        heading: "Block 3",
                        paragraph:
                            "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                        url: "#",
                        image: "https://designertravel.co.uk/storage/images/3456477167701739313157894951831739204864723n/3456477167701739313157894951831739204864723n_400.webp",
                        rating: 4,
                        button_text: "Button Text",
                        label: "",
                        label_colour: "",
                    },
                },
            ],
        },

        categories: ["contents"],

        fields: [
            {
                name: "features",
                label: "Features",
                type: "repeater",
                mapFunction,
                fields: [
                    {
                        name: "image",
                        label: "Image",
                        type: "media-library",
                        endpoint,
                    },
                    {
                        name: "heading",
                        label: "Heading",
                        type: "text",
                    },
                    {
                        name: "location",
                        label: "Location",
                        type: "text",
                    },
                    {
                        name: "paragraph",
                        label: "Paragraph",
                        type: "textarea",
                    },
                    {
                        name: "button_text",
                        label: "Button Text",
                        type: "text",
                    },
                    {
                        name: "url",
                        label: "Url",
                        type: "text",
                    },
                    {
                        name: "rating",
                        label: "Rating",
                        type: "number",
                        max: 5,
                        min: 0,
                    },
                    {
                        name: "label",
                        label: "Label",
                        type: "text",
                    },
                    {
                        name: "colours",
                        label: "Colours",
                        type: "collapse",
                        fields: [
                            {
                                name: "label_colour",
                                label: "Label Colour",
                                type: "colour",
                            },
                        ],
                    },
                ],
            },
            {
                name: "extras",
                label: "Extras",
                type: "collapse",
                fields: [
                    {
                        name: "bottom_space",
                        label: "Space Below",
                        type: "select",
                        options: [
                            {
                                label: "None",
                                value: "none",
                            },
                            {
                                label: "Small",
                                value: "small",
                            },
                            {
                                label: "Medium",
                                value: "medium",
                            },
                            {
                                label: "Large",
                                value: "large",
                            },
                        ],
                    },
                    {
                        name: "width",
                        label: "Width",
                        type: "select",
                        options: [
                            {
                                label: "Full",
                                value: "full",
                            },
                            {
                                label: "Standard",
                                value: "standard",
                            },
                            {
                                label: "Reduced",
                                value: "reduced",
                            },
                        ],
                    },
                ],
            },
        ],
    }),
};
</script>
