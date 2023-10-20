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
        <div
            v-if="content?.showLatest"
            class="mb-4 rounded-lg bg-yellow-200 p-2"
        >
            <p class="font-bold">
                This block will show the most recent blogs that have been
                uploaded.
            </p>
        </div>
        <ul
            class="max-w-lg mx-auto grid gap-8 lg:grid-cols-3 lg:max-w-none px-8 xl:px-0"
        >
            <li
                v-for="feature in content?.features"
                class="cursor-pointer min-h-400"
            >
                <div
                    class="relative flex flex-col overflow-hidden transition duration-500 rounded-lg shadow-lg hover:shadow-2xl"
                >
                    <div
                        class="flex-shrink-0 overflow-hidden transition duration-700 ease-in-out"
                    >
                        <img
                            class="object-cover w-full h-60 image-roll"
                            :src="feature.content.image"
                            alt=""
                        />
                    </div>

                    <div
                        class="flex flex-col justify-between p-6 mb-5 bg-white h-55 max-h-60"
                    >
                        <div class="flex-1">
                            <div class="overflow-hidden h-22 max-h-22">
                                <p
                                    class="font-serif text-2xl font-medium text-zinc-700 break-words"
                                >
                                    {{ feature.content.heading }}
                                </p>
                                <p class="mt-1 text-sm">
                                    {{ feature.content.author }}
                                </p>
                            </div>
                            <p
                                v-html="nl2br(feature.content.paragraph)"
                                class="mt-4 text-base text-zinc-500 overflow-hidden"
                                style="height: 150px"
                            />
                        </div>
                    </div>
                    <div
                        class="p-6 mt-3 rounded-bl-lg rounded-br-lg bg-zinc-50 md:px-8"
                    >
                        <div
                            class="text-base font-medium text-indigo-900 capitalize hover:text-indigo-600"
                        >
                        {{ feature.content.button_text }} <span aria-hidden="true"> →</span>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </Block>
</template>

<script>
import { defineComponent } from "vue";
import { Block, BlockMixin, defineBlock, TextNode } from "@senses/builder";
import axios from "axios";
import Icon from "../../icons/svg/shark/grid-3-image-no-rating.svg?raw";
import mapFunction from '../../builderFileMap';
import endpoint from '../../builderFileUrl';

export default defineComponent({
    components: { Block, TextNode },
    mixins: [BlockMixin],

    mounted() {
        this.getLastestBlogs();
    },
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
        getLastestBlogs() {
            axios.get("/api/retrieve-latest-blogs").then((response) => {
                console.log(response);
            });
        },
    },
    block: defineBlock({
        name: "repeaters.blog-card-repeater",
        displayName: "Blog Card",
        categories: ["contents"],
        icon: Icon,
        defaultContent: {
            width: "standard",
            bottom_space: "small",
            showLatest: false,
            features: [
                {
                    content: {
                        heading: "Block 1",
                        paragraph:
                            "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                        url: "#",
                        author: "Author",
                        image: "https://designertravel.co.uk/storage/images/splitcroatiapbay/splitcroatiapbay_1200.webp",
                        button_text: "Read more",
                    },
                },
                {
                    content: {
                        heading: "Block 2",
                        paragraph:
                            "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                        url: "#",
                        author: "Author",
                        image: "https://designertravel.co.uk/storage/images/splitcroatiapbay/splitcroatiapbay_1200.webp",
                        button_text: "Read more",
                    },
                },
                {
                    content: {
                        heading: "Block 3",
                        paragraph:
                            "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                        url: "#",
                        author: "Author",
                        image: "https://designertravel.co.uk/storage/images/splitcroatiapbay/splitcroatiapbay_1200.webp",
                        button_text: "Read more",
                    },
                },
            ],
        },
        fields: [
            {
                name: "showLatest",
                label: "Show Most Recent",
                type: "toggle",
            },
            {
                name: "features",
                label: "Features",
                type: "repeater",
                mapFunction,
                fields: [
                    {
                        name: "image",
                        label: "Select Image",
                        type: "media-library",
                        endpoint,
                    },
                    {
                        name: "heading",
                        label: "Heading",
                        type: "text",
                    },
                    {
                        name: "author",
                        label: "Author",
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
