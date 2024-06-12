<template>
    <div class="h-full w-1/4 min-w-64 space-y-4">
        <Meta>
            <MetaItem v-if="user()?.email?.endsWith('@senses.co.uk')">
                <template #title>ID</template>
                <Text>{{ file.id ?? 'N/A' }}</Text>
            </MetaItem>

            <MetaItem>
                <template #title>Uploaded At</template>
                <Text>{{ formatDateTime(file.created_at) }}</Text>
            </MetaItem>

            <MetaItem>
                <template #title>Uploaded By</template>
                <Tag :colour="file?.creator.colour">{{ file?.creator.full_name }}</Tag>
            </MetaItem>

            <MetaItem v-if="file?.folder">
                <template #title>Folder</template>
                <Text>{{ file.folder }}</Text>
            </MetaItem>

            <MetaItem v-if="file?.mime_type">
                <template #title>Mime Type</template>
                <Text>{{ file.mime_type }}</Text>
            </MetaItem>

            <MetaItem v-if="file?.size">
                <template #title>Size</template>
                <Text>{{ calculateSize(file.size) }}</Text>
            </MetaItem>

            <MetaItem v-if="file?.pivot">
                <template #title>Relations</template>
                <Tag colour="purple" class="capitalize" :href="relationLink">{{ relationText }}</Tag>
            </MetaItem>

        </Meta>
    </div>
</template>

<script>
import Tag from '../../Ui/Tags/Tag.vue';
import Text from '../../Ui/Text/Text.vue';

import formatDateTime from '../../../Filters/FormatDateTime';
import user from '../../../Support/user';

import Meta from '../Meta/Meta.vue';
import MetaItem from '../Meta/MetaItem.vue';

export default {
    components: {
        Meta,
        MetaItem,

        Text,
        Tag,
    },
    props: {
        file: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            user
        };
    },
    computed: {
        relationText() {
            if (this.file.pivot) {
                return `${this.file.pivot.fileable_type} ${this.file.pivot.fileable_id}`;
            }

            return 'No relations';
        },
        relationLink() {
            if (this.file.pivot) {
                return `/${this.file.pivot.fileable_type}/${this.file.pivot.fileable_id}`;
            }

            return '#';
        },
        center() {
            return this.markers[0].coordinates;
        }
    },
    watch: {
        file() {
            this.markers = [];
            this.setupMarkers();
        },
    },
    created() {
        this.setupMarkers();
    },
    methods: {
        formatDateTime,

        calculateSize(size) {
            const i = Math.floor(Math.log(size) / Math.log(1024));
            return `${(size / Math.pow(1024, i)).toFixed(2) * 1} ${['B', 'KB', 'MB', 'GB', 'TB'][i]}`;
        },
        
        setupMarkers() {
            this.markers = [];
            if (this.file.geom !== null) {
                const markers = [];
                const points = [];
    
                const markerObject = this.setupMarkerDataFromCoordinates([
                    this.file.geom.y,
                    this.file.geom.x
                ], 'sky');
    
                if (markerObject) {
                    markerObject.pulse = true;
                    markerObject.label = 'Image Location';
                    markers.push(markerObject);
                    points.push(markerObject.position);
                }
    
                this.markers = markers;
            }
        }
    },
};
</script>