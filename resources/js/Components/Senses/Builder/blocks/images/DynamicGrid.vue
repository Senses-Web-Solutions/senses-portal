<template>
    <Block
        :class="{
            'w-full': content.width === 'full',
            'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8':
                content.width === 'standard',
            'max-w-5xl mx-auto px-4 sm:px-6 lg:px-8':
                content.width === 'reduced',
            'mb-0': content.bottom_space === 'none',
            'mb-10': content.bottom_space === 'small',
            'mb-16': content.bottom_space === 'medium',
            'mb-24': content.bottom_space === 'large',
        }"
    >
        <div
            class="grid grid-cols-6 gap-x-3 gap-y-3 rounded-2xl overflow-hidden shadow-lg"
        >
            <div
                v-for="feature in content?.features"
                class="h-48 overflow-hidden object-cover relative"
                :class="{
                    'col-span-3': feature.content.span === 'half',
                    'col-span-6': feature.content.span === 'full',
                    'col-span-2': feature.content.span === 'third',
                }"
            >
                <h3
                    class="absolute top-3 left-4 text-white font-serif text-3xl tracking-wider z-40"
                >
                    {{ feature.content.heading }}
                </h3>
                <div class="image-roll h-full">
                    <div
                        class="absolute top-0 left-0 h-full w-full bg-zinc-800 opacity-50"
                    ></div>
                    <img
                        class="h-full w-full object-cover shadow-lg"
                        :src="feature.content.image"
                        alt=""
                    />
                </div>
            </div>
        </div>
    </Block>
</template>

<script>
import { defineComponent } from "vue";
import { Block, BlockMixin, defineBlock, TextNode } from "@senses/builder";
import axios from "axios";
import Icon from "../../icons/svg/shark/layouts-three-equal-cols.svg?raw";
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

        getLastestBlogs() {
            axios.get("/api/retrieve-latest-blogs").then((response) => {
                console.log(response);
            });
        },
    },
    block: defineBlock({
        name: "images.dynamic-grid",
        displayName: "Dynamic Grid",
        categories: ["images"],
        icon: Icon,
        defaultContent: {
            bottom_space: "small",
            width: "standard",
            features: [
                {
                    content: {
                        heading: "Block 1",
                        url: "#",
                        span: "half",
                        image: "https://media.istockphoto.com/id/1205214235/photo/path-through-sunlit-forest.jpg?s=612x612&w=0&k=20&c=-AS1aTz85kcZ2X7E8n2iFlm6dsdIMyWGWrSDQ1o-f_0=",
                    },
                },
                {
                    content: {
                        heading: "Block 2",
                        url: "#",
                        span: "half",
                        image: "https://media.istockphoto.com/id/1205214235/photo/path-through-sunlit-forest.jpg?s=612x612&w=0&k=20&c=-AS1aTz85kcZ2X7E8n2iFlm6dsdIMyWGWrSDQ1o-f_0=",
                    },
                },
                {
                    content: {
                        heading: "Block 3",
                        url: "#",
                        span: "full",
                        image: "https://media.istockphoto.com/id/1205214235/photo/path-through-sunlit-forest.jpg?s=612x612&w=0&k=20&c=-AS1aTz85kcZ2X7E8n2iFlm6dsdIMyWGWrSDQ1o-f_0=",
                    },
                },
            ],
        },
        fields: [
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
                        name: "span",
                        label: "Span",
                        type: "select",
                        options: [
                            {
                                label: "Full",
                                value: "full",
                            },
                            {
                                label: "Half",
                                value: "half",
                            },
                            {
                                label: "Third",
                                value: "third",
                            },
                        ],
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
