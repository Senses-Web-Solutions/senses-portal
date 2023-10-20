<template>
    <Block
        class="relative"
        :class="{
            'mb-0': content.bottom_space == 'none',
            'mb-10': content.bottom_space == 'small',
            'mb-16': content.bottom_space == 'medium',
            'mb-24': content.bottom_space == 'large',
        }"
    >
        <div class="max-w-screen">
            <div class="h-80">
                <div class="absolute inset-0 overflow-hidden">
                    <video
                        v-if="extension === 'mp4'"
                        class="w-full h-full object-cover"
                        autoplay
                        muted
                        loop
                        playsinline
                    >
                        <source :src="content.selectedImage" type="video/mp4" />
                    </video>
                    <img
                        v-else
                        class="w-full h-full object-cover"
                        :src="content.selectedImage"
                        alt=""
                    />

                    <div
                        class="absolute inset-0 bg-zinc-400 bg-opacity-50 mix-blend-multiply"
                    ></div>
                </div>

                <div
                    class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2"
                >
                    <div class="font-serif text-white z-50 text-2xl">
                        <!-- <div class="bg-white rounded-2xl p-8 shadow-lg mx-auto"> -->
                        {{ content.paragraph }}
                        <!-- </div> -->
                    </div>
                </div>
            </div>
        </div>
    </Block>
</template>

<script>
import { defineComponent } from "vue";
import { Block, BlockMixin, defineBlock, TextNode } from "@senses/builder";
import axios from "axios";
import Icon from "../../icons/svg/shark/wide-image-with-text.svg?raw";
import mapFunction from '../../builderFileMap';
import endpoint from '../../builderFileUrl';

export default defineComponent({
    components: { Block, TextNode },
    mixins: [BlockMixin],

    computed: {
        extension() {
            return this.content.image.split(".").pop();
        },
    },

    mounted() {
        this.getLastestBlogs();
    },
    methods: {
        input(e) {
            const content = this.content;
            content.text = e.target.innerText;
            this.$emit("update:content", content);
        },

        getLastestBlogs() {
            axios.get("/api/retrieve-latest-blogs").then((response) => {
                console.log(response);
            });
        },
    },
    block: defineBlock({
        name: "images.full-width-image-banner",
        displayName: "Full Width Image Banner",
        categories: ["images"],
        icon: Icon,
        defaultContent: {
            image: "https://designertravel.co.uk/storage/images/splitcroatiapbay/splitcroatiapbay_1200.webp",
            paragraph:
                "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
            url: "#",
            alignment: "centre",
            bottom_space: "small",
        },
        fields: [
            {
                name: "selectedImage",
                label: "Select Image",
                type: "media-library",
                endpoint,
                mapFunction
            },
            {
                name: "section2",
                label: "Content",
                type: "collapse",
                fields: [
                    {
                        name: "paragraph",
                        label: "Text",
                        type: "text",
                    },
                    {
                        name: "alignment",
                        label: "Alignment",
                        type: "select",
                        options: [
                            {
                                label: "Left",
                                value: "left",
                            },
                            {
                                label: "Centre",
                                value: "centre",
                            },
                            {
                                label: "Right",
                                value: "right",
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
                ],
            },
        ],
    }),
});
</script>
