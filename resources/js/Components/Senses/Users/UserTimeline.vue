<template>
    <component :is="as" flush header-class="min-h-[54px]">
        <template #title>Timeline</template>
        <slot></slot>
        <Timeline :url="url" :expanded="expanded" :viewMoreUrl="viewMoreUrl"/>
    </component>
</template>
<script>
import Card from "../../Ui/Cards/Card.vue";
import Panel from '../../Ui/Panels/Panel.vue';
import Timeline from "../Timelines/Timeline.vue";

export default {
    components: { Card, Timeline, Panel },
    props:{
        data: {
            required: true,
            type: Object
        },
        as: {
            type: String,
            default: 'Card'
        },
        expanded: {
            type: Boolean,
            default: false,
        },
        limit: {
            type: Number,
            default: 999,
        },
        viewMoreUrl: {
            type: String,
            default: '',
        }
    },
    computed: {
        url() {
            return `/api/v2/users/${this.data.id}/user-action-logs?limit=${this.limit}`;
        }
    }
};
</script>
