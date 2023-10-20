<template>
    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px my-2" aria-label="Pagination">
        <a href="#" class="relative inline-flex items-center px-1 py-1 rounded-l-md border border-zinc-300 bg-white font-medium text-zinc-500 hover:bg-zinc-50" @click.prevent="loadPage(rows.prev_page_url)">
            <span class="sr-only">Previous</span>
            <ChevronLeftIcon class="h-5 w-5" aria-hidden="true" />
        </a>
        <template v-for="(link,index) in links" :key="index">
            <a href="#" :class="linkClasses(link)" @click.prevent="loadPage(link.url)" v-html="link.label"></a>
        </template>
        <a href="#" class="relative inline-flex items-center px-1 py-1 rounded-r-md border border-zinc-300 bg-white font-medium text-zinc-500 hover:bg-zinc-50" @click.prevent="loadPage(rows.next_page_url)">
            <span class="sr-only">Next</span>
            <ChevronRightIcon class="h-5 w-5" aria-hidden="true" />
        </a>
    </nav>
</template>

<script>
import {
    ChevronLeftIcon,
    ChevronRightIcon
} from '@heroicons/vue/solid'

export default {
    components: {
        ChevronLeftIcon,
        ChevronRightIcon,
    },
    props: {
        rows: {
            type: Object
        }
    },
    computed: {
        links() {
            let links = [];
            if (this.rows.links) {
                links = this.rows.links;
                links.shift();
                links.pop();
            }

            return links;
        },
    },
    methods: {
        linkClasses(link) {
            if (link.active) {
                return 'z-10 bg-primary-50 border-primary-500 text-primary-600 relative inline-flex items-center px-2 py-1 border font-medium';
            }

            return 'bg-white border-zinc-300 text-zinc-500 hover:bg-zinc-50 relative inline-flex items-center px-2 py-1 border font-medium';
        },

        loadPage(url) {
            if (url) {
                this.$emit('page', url);
            }
        }
    }

}
</script>
