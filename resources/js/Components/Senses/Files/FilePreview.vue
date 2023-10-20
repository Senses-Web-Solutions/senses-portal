<template>
    <a
        :href="url"
        target="_blank"
    >
        <img
            :src="previewUrl"
            class="w-1/3"
        />
    </a>
</template>
<script>
import axios from 'axios';

export default {
    props: {
        file: {
            type: Object,
            default: () => ({}),
        },
        id: {
            type: Number,
            default: null,
        },
    },
    computed: {
        previewUrl() {
            if (this.file) {
                return this.proxyFile.preview_url ?? this.proxyFile.original_url;
            }
        },
        url() {
            if (this.file) {
                return this.proxyFile.original_url;
            }
        },
    },
    data() {
        return {
            proxyFile: this.file,
        };
    },
    mounted() {
        if (this.id) {
            this.getFile();
        }
    },
    methods: {
        getFile() {
            axios
                .get('/api/v2/files/' + this.id)
                .then((response) => {
                    this.proxyFile = response.data;
                })
                .catch((error) => {
                    this.error = error;
                });
        },
    },
};
</script>
