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
        <div class="mx-auto grid grid-cols-2">
            <div v-if="content.alignment === 'right'" class="col-span-1">
                <img
                    class="h-full object-cover object-center"
                    :src="content.selectedImage"
                    alt=""
                />
            </div>
            <div class="px-6 col-span-1">
                <div class="py-16">
                    <p
                        class="text-base font-semibold leading-7 text-indigo-600 font-serif"
                    >
                        {{ content.category }}
                    </p>
                    <h1
                        class="mt-2 text-3xl font-medium font-serif tracking-tight text-zinc-700 sm:text-4xl"
                    >
                        {{ content.title }}
                    </h1>
                    <!-- <p class="mt-6 text-xl leading-8 text-zinc-700">{{ content.subtitle }}</p> -->
                    <p
                        v-html="nl2br(content.paragraph, true)"
                        class="mt-10 max-w-xl font-sans-serif text-base leading-7 text-zinc-700 lg:max-w-none"
                    />

                    <TextNode
                        v-if="content.button_text"
                        basic
                        class="pointer inline-block px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-black hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 mt-10"
                        placeholder="Enter Text"
                        :model-value="content.button_text"
                        @update:modelValue="update('text', $event)"
                    ></TextNode>
                </div>
            </div>
            <div v-if="content.alignment === 'left'" class="col-span-1">
                <img
                    class="h-full object-cover object-center"
                    :src="content.selectedImage"
                    alt=""
                />
            </div>
        </div>
    </Block>
</template>
<script lang="ts">
import { Block, BlockMixin, defineBlock, TextNode } from "@senses/builder";
import { defineComponent } from "vue";

import Icon from "../../icons/svg/content-category-text-left-img-right.svg?raw";
import mapFunction from '../../builderFileMap';
import endpoint from '../../builderFileUrl';

export default defineComponent({
    components: { Block, TextNode },
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
        name: "contents.text-with-image",
        displayName: "Text & Image",
        icon: Icon,
        defaultContent: {
            width: "standard",
            bottom_space: "small",
            alignment: "right",
            category: "Category",
            title: "Title",
            paragraph:
                "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Elementum sagittis vitae et leo duis ut.",
            justify: "left",
            selectedImage:
                "https://images.unsplash.com/photo-1440778303588-435521a205bc?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=2070&q=80",
            button_text: "",
            url: "#",
        },
        categories: ["contents"],
        fields: [
            {
                name: "alignment",
                label: "Alignment",
                type: "select",
                options: [
                    {
                        label: "Image Right, Text Left",
                        value: "left",
                    },
                    {
                        label: "Image Left, Text Right",
                        value: "right",
                    },
                ],
            },
            {
                name: "selectedImage",
                label: "Select Image",
                type: "media-library",
                endpoint,
                mapFunction,
            },
            {
                name: "content",
                label: "Content",
                type: "collapse",
                fields: [
                    {
                        name: "category",
                        label: "Category",
                        type: "text",
                    },
                    {
                        name: "title",
                        label: "Title",
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
                        label: "URL",
                        type: "text",
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
});
</script>
