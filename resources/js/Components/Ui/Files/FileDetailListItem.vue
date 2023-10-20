<template>
    <div class="flex items-center gap-4 p-4 transition cursor-pointer hover:bg-zinc-200 active:bg-zinc-300">
        <img
            v-if="file && (isImage && file.original_url != null)"
            class="object-cover w-10 h-10 rounded-md select-none"
            style="clip-path: url(#documentClipPath)"
            :src="file.preview_url ?? file.original_url"
        />
        <div
            v-else-if="file"
            class="object-cover rounded-md select-none"
        >
            <DocumentTextIcon class="w-10 h-10 mx-auto my-auto text-zinc-400 " />
        </div>
        <div>
            <slot />
        </div>
        <div
            class="ml-auto shrink-0"
            v-if="$slots.end"
        >
            <slot name="end" />
        </div>
    </div>
</template>
<script>
import {
    DocumentTextIcon
} from '@heroicons/vue/outline';
export default {
    components: {DocumentTextIcon},

    props: {
        file: {
            default: null,
        },
    },

    computed: {
        isImage() {
            return this.file.mime_type && this.file.mime_type.startsWith('image/');
        },
    }
};
</script>
