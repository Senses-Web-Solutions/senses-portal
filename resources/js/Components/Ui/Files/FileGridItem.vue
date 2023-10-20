<template>
    <div class="group text-sm text-center transition rounded-md" :class="{'cursor-pointer': hoverEffect}">

        <div class="relative">
            <img
            v-if="file && (isImage && file.original_url != null)"
            class="object-cover mx-auto rounded-md select-none"
            style="clip-path: url(#documentClipPath)"
            :class="classes"
            :src="file.preview_url ?? file.original_url"
        />
        <div v-else-if="file" class="object-cover mx-auto rounded-md select-none" :class="classes">
            <component :is="icon" class="mx-auto my-auto text-zinc-400" :class="classes" />
        </div>

        <div class="absolute top-1 right-1 bg-white rounded-full" v-if="removeable" @click.stop="remove">
            <XCircleIcon class="w-5 h-5 text-zinc-900  hover:text-red-900 cursor-pointer"/>
        </div>

        </div>



        <div class="w-20 pt-2" v-if="!condensed">
            <slot />
        </div>

    </div>
</template>

<script>
import {
    DocumentTextIcon,
    CloudUploadIcon
} from '@heroicons/vue/outline';
import { XCircleIcon } from '@heroicons/vue/solid';

export default {
    components: {
        DocumentTextIcon,
        CloudUploadIcon,
        XCircleIcon
    },
    emits:['remove'],
    props: {
        hoverEffect: {
            type: Boolean,
            default: true
        },
        file: {
            default: null,
        },
        size: {
            type: String,
            default: 'md',
            validator(value) {
                return ['custom', 'xs', 'sm', 'md', 'lg'].includes(
                    value
                );
            },
        },
        customClasses: {
            type: String,
            required: false
        },
        removeable:{
            type:Boolean,
            default:false
        },
        condensed: Boolean,
    },
    computed: {
        isImage() {
            return this.file.mime_type && this.file.mime_type.startsWith('image/');
        },

        isPdf() {
            return this.file.mime_type && this.file.mime_type.toLowerCase() === 'application/pdf';
        },

        classes() {
            let classes = [];

            const sizes = {
                custom: this.customClasses ?? '', // byop bring your own padding.
                xs: 'w-14 h-14',
                sm: 'w-16 h-16',
                md: 'w-20 h-20',
                lg: 'w-24 h-24',
            };

            if (this.hoverEffect) {
                classes.push('hover:opacity-75');
            }

            if (sizes[this.size]) {
                classes.push(sizes[this.size]);
            }

            return classes;
        },

        icon() {
            if (this.file.pending) {
                return 'DocumentTextIcon'; // todo make pending look nicer
            }
            return 'DocumentTextIcon';
        }
    },
    methods:{
        remove() {
            this.$emit('remove', this.file);
        }
    }
};
</script>
