<template>
    <TimelineItem :datetime="actionLog.logged_at" :last="last">
        <template #icon>
            <AnnotationIcon class="h-4 w-4 text-green-500" />
        </template>
        <template #title>
            Chat Started
        </template>
    </TimelineItem>
</template>

<script>
import { AnnotationIcon } from '@heroicons/vue/outline';
import TimelineItem from '../../Timelines/TimelineItem.vue';

export default {
    components: {
        AnnotationIcon,
        TimelineItem
    },
    props: {
        actionLog: {
            type: Object,
            required: true
        },
        last: {
            type: Boolean,
            default: false
        }
    },
}
</script>