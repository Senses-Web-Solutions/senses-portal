<template>
    <Card>
        <template #title>Information</template>
        <template #description>Basic information about this {{ model }}.</template>
        <dl class="grid grid-cols-1 gap-x-4 gap-y-8" :class="'sm:grid-cols-' + columns">
            <div :class="item.colspan ? 'col-span-' + item.colspan : 'col-span-1'" v-for="item in items">
                <StrongText>{{ item.title }}</StrongText>
                <Colour v-if="item.type && item.type === 'colour'" :colour="get(data, item.field)" class="mt-1"></Colour>
                <div v-else-if="item.type && item.type === 'boolean'" class="mt-1">
                    <BooleanText :data="get(data, item.field)"/>
                </div>
                <Text v-else>{{ get(data, item.field) }}</Text>
            </div>
        </dl>
    </Card>
</template>
<script>
import Card from './Card.vue';
import {get} from "lodash-es";
import StrongText from "../Text/StrongText.vue";
import Text from "../Text/Text.vue";
import Colour from "../Colour.vue";
import BooleanText from '../Text/BooleanText.vue';
export default {

    components: {Card, StrongText, Text, BooleanText, Colour},

    props: {
        columns: {
            type: String,
            default: '2'
        },
        items: {
            type: Array,
        },
        data: {
            type: Object,
            required: true,
        },
        model: {
            type: String,
            default: 'model'
        }
    },
    methods: {
        get,
    }
}
</script>
