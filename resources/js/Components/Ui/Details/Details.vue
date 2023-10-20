<template>
    <Card flush>
        <DetailsHeader :colour="colour">
            <template #header>
                <slot name="header">
                </slot>
            </template>
            <template #title>
                <slot name="title"></slot>
            </template>
            <template #subtitle>
                <slot name="subtitle"></slot>
            </template>
            <template #action>
                <slot name="action"></slot>
            </template>
        </DetailsHeader>
        <slot name="alerts"></slot>
        <div class="border-t border-zinc-200 divide-y divide-zinc-200 dark:divide-zinc-800">
            <slot></slot>
        </div>
    </Card>
</template>
<script>
import DetailsHeader from './DetailsHeader.vue';
import Card from '../Cards/Card.vue';

export default {
    components: {DetailsHeader, Card},
    props: {
        colour: {
            type: String
        }
    }
}
</script>
