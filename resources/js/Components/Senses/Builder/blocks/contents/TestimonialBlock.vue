<template>
    <Block
        :class="{ 'w-full': content.width === 'full', 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8': content.width === 'standard', 'max-w-5xl mx-auto px-4 sm:px-6 lg:px-8': content.width === 'reduced', 'mb-0': content.bottom_space == 'none', 'mb-10': content.bottom_space == 'small', 'mb-16': content.bottom_space == 'medium', 'mb-24': content.bottom_space == 'large' }">
        <!-- <div class="bg-white">
            <div class="grid grid-cols-2 gap-x-8">
                <div v-for="feature in content?.features" class="col-span-1">
                    <div class="bg-white overflow-hidden shadow rounded-lg border border-zinc-100">
                            <div class="px-4 py-3 sm:p-5">
                                <div class="flex-shrink-0 flex my-3 space-x-2 justify-center">
                                                                            <div class="text-yellow-500" aria-hidden="true">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5
                                            w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M9.049 2.927c.3-.921
                                            1.603-.921 1.902 0l1.07 3.292a1 1
                                            0 00.95.69h3.462c.969 0 1.371 1.24.588
                                            1.81l-2.8 2.034a1 1 0 00-.364
                                            1.118l1.07 3.292c.3.921-.755 1.688-1.54
                                            1.118l-2.8-2.034a1 1 0 00-1.175
                                            0l-2.8
                                            2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1
                                            1 0
                                            00-.364-1.118L2.98
                                            8.72c-.783-.57-.38-1.81.588-1.81h3.461a1
                                            1 0
                                            00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                            </div>
                                                                            <div class="text-yellow-500" aria-hidden="true">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5
                                            w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M9.049 2.927c.3-.921
                                            1.603-.921 1.902 0l1.07 3.292a1 1
                                            0 00.95.69h3.462c.969 0 1.371 1.24.588
                                            1.81l-2.8 2.034a1 1 0 00-.364
                                            1.118l1.07 3.292c.3.921-.755 1.688-1.54
                                            1.118l-2.8-2.034a1 1 0 00-1.175
                                            0l-2.8
                                            2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1
                                            1 0
                                            00-.364-1.118L2.98
                                            8.72c-.783-.57-.38-1.81.588-1.81h3.461a1
                                            1 0
                                            00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                            </div>
                                                                            <div class="text-yellow-500" aria-hidden="true">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5
                                            w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M9.049 2.927c.3-.921
                                            1.603-.921 1.902 0l1.07 3.292a1 1
                                            0 00.95.69h3.462c.969 0 1.371 1.24.588
                                            1.81l-2.8 2.034a1 1 0 00-.364
                                            1.118l1.07 3.292c.3.921-.755 1.688-1.54
                                            1.118l-2.8-2.034a1 1 0 00-1.175
                                            0l-2.8
                                            2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1
                                            1 0
                                            00-.364-1.118L2.98
                                            8.72c-.783-.57-.38-1.81.588-1.81h3.461a1
                                            1 0
                                            00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                            </div>
                                                                            <div class="text-yellow-500" aria-hidden="true">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5
                                            w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M9.049 2.927c.3-.921
                                            1.603-.921 1.902 0l1.07 3.292a1 1
                                            0 00.95.69h3.462c.969 0 1.371 1.24.588
                                            1.81l-2.8 2.034a1 1 0 00-.364
                                            1.118l1.07 3.292c.3.921-.755 1.688-1.54
                                            1.118l-2.8-2.034a1 1 0 00-1.175
                                            0l-2.8
                                            2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1
                                            1 0
                                            00-.364-1.118L2.98
                                            8.72c-.783-.57-.38-1.81.588-1.81h3.461a1
                                            1 0
                                            00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                            </div>
                                                                            <div class="text-yellow-500" aria-hidden="true">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5
                                            w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M9.049 2.927c.3-.921
                                            1.603-.921 1.902 0l1.07 3.292a1 1
                                            0 00.95.69h3.462c.969 0 1.371 1.24.588
                                            1.81l-2.8 2.034a1 1 0 00-.364
                                            1.118l1.07 3.292c.3.921-.755 1.688-1.54
                                            1.118l-2.8-2.034a1 1 0 00-1.175
                                            0l-2.8
                                            2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1
                                            1 0
                                            00-.364-1.118L2.98
                                            8.72c-.783-.57-.38-1.81.588-1.81h3.461a1
                                            1 0
                                            00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                            </div>
                                                                    </div>

                                <div class="text-center">
                                    <div class="">
                                        <p class="text-xl font-medium text-zinc-700 font-serif h-14 overflow-hidden mb-3">
                                            "We have just returned from our trip to Rome booked by Allie. She was amazing at giving us hotel o..."
                                        </p>
                                    </div>

                                    <p class="text-md text-zinc-700">
                                        Pam Ormerod
                                    </p>
                                    <a href="/testimonials">
                                        <p class="text-sm text-zinc-500 capitalize underline">
                                            Read All Reviews
                                        </p>
                                    </a>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div> -->

        <div class="relative max-w-7xl mx-auto px-4">
            <div class="owl-carousel owl-theme">
                <div v-for="(feature, index) in content?.features" :key="index" class="bg-white overflow-hidden shadow rounded-lg border border-zinc-100">
                    <div class="px-4 py-3 sm:p-5">
                        <div class="flex-shrink-0 flex my-3 space-x-2 justify-center">
                            <div v-for="i in 5" class="text-yellow-500" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5
                                            w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921
                                            1.603-.921 1.902 0l1.07 3.292a1 1
                                            0 00.95.69h3.462c.969 0 1.371 1.24.588
                                            1.81l-2.8 2.034a1 1 0 00-.364
                                            1.118l1.07 3.292c.3.921-.755 1.688-1.54
                                            1.118l-2.8-2.034a1 1 0 00-1.175
                                            0l-2.8
                                            2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1
                                            1 0
                                            00-.364-1.118L2.98
                                            8.72c-.783-.57-.38-1.81.588-1.81h3.461a1
                                            1 0
                                            00.951-.69l1.07-3.292z" />
                                </svg>
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="">
                                <p class="text-xl font-medium text-zinc-700 font-serif h-14 overflow-hidden mb-3">
                                    "{{ feature.content?.testimony }}..."
                                </p>
                            </div>

                            <p class="text-md text-zinc-700">
                                {{ feature.content?.author }}
                            </p>
                            <a href="/testimonials">
                                <p class="text-sm text-zinc-500 capitalize underline">
                                    Read All Reviews
                                </p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Block>
</template>
<script>

import { Block, BlockMixin, defineBlock, TextNode } from "@senses/builder";
import Icon from '../../icons/svg/shark/grid-3-ratings.svg?raw';
import axios from 'axios';

export default {
    components: {
        Block,
        TextNode
    },

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
        },
        retrieveTestimonials() {
            try {
                axios.get('/api/retrieve-testimonials').then(response => {
                    this.content.features = response.data;
                })
            } catch (error) {
                console.log(error);
            }
        }
    },
    mounted() {
        this.retrieveTestimonials();

        $(document).ready(function () {
            $('.owl-carousel').owlCarousel({
                margin: 20,
                loop: true,
                autoplay: true,
                autoplayHoverPause: true,
                autoplayTimeout: 5000,
                items: 2,
                responsiveClass: true,
                responsive: {
                    0: {
                        items: 1,

                    },
                    1000: {
                        items: 2,
                    },
                }

            });
        });
    },
    block: defineBlock({

        name: 'contents.testimonials-block',
        displayName: 'Testimonials Block',
        icon: Icon,
        categories: ['contents'],

        defaultContent: {
            title: 'Testimonials Block',
            width: 'standard',
            bottom_space: 'small',
            features: [
                {
                    content: {
                        heading: 1,
                        id: 1,
                        testimony: "BEAUTIFUL IKOS ANDALUSIA, ESTEPONA SPAINHotel Ikos Andalusia Spain 5 star luxury I think y...",
                        author: 'John Smith'
                    },
                },
                {
                    content: {
                        heading: 2,
                        id: 2,
                        testimony: "BEAUTIFUL IKOS ANDALUSIA, ESTEPONA SPAINHotel Ikos Andalusia Spain 5 star luxury I think y...",
                        author: 'John Smith'
                    },
                },
                {
                    content: {
                        heading: 3,
                        id: 3,
                        testimony: "BEAUTIFUL IKOS ANDALUSIA, ESTEPONA SPAINHotel Ikos Andalusia Spain 5 star luxury I think y...",
                        author: 'John Smith'
                    },
                },
                {
                    content: {
                        heading: 4,
                        id: 4,
                        testimony: "BEAUTIFUL IKOS ANDALUSIA, ESTEPONA SPAINHotel Ikos Andalusia Spain 5 star luxury I think y...",
                        author: 'John Smith'
                    },
                },
                {
                    content: {
                        heading: 5,
                        id: 5,
                        testimony: "BEAUTIFUL IKOS ANDALUSIA, ESTEPONA SPAINHotel Ikos Andalusia Spain 5 star luxury I think y...",
                        author: 'John Smith'
                    },
                },
                {
                    content: {
                        heading: 6,
                        id: 6,
                        testimony: "BEAUTIFUL IKOS ANDALUSIA, ESTEPONA SPAINHotel Ikos Andalusia Spain 5 star luxury I think y...",
                        author: 'John Smith'
                    },
                },
            ]
        },

        fields: [
            {
                name: 'title',
                label: 'Title',
                type: 'text',
            },
            {
                name: 'features',
                label: 'Features',
                type: 'repeater',
                fields: [

                    {
                        name: 'testimony',
                        label: 'Testimony',
                        type: 'textarea',
                    },
                    {
                        name: 'author',
                        label: 'Author',
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
}
</script>
