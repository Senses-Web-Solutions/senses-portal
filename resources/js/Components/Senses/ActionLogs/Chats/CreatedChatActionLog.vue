<template>
    <TimelineItem :datetime="actionLog.logged_at">
        <template #icon>
            <AnnotationIcon class="h-4 w-4 text-green-500" />
        </template>
        <template #title>
            <span class="text-black">{{ author }}</span> started a chat
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
    },
    computed: {
        author() {
            return this.actionLog.data.author ?? 'Visitor';
        },
    },
}
</script>