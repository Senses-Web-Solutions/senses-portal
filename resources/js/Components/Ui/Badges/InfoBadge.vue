<template>
    <Badge :size="size" type="info" :outline="outline">
        <slot></slot>
    </Badge>
</template>
<script>
import Badge from './Badge.vue';

export default {
    components: {
        Badge
    },
    props: {
        size: {
            type: String,
            default: "sm",
            validator(value) {
                return ["xs", "sm"].includes(value);
            },
        },
        outline: {
            type: Boolean,
            default: false
        }
    },
}
</script>
