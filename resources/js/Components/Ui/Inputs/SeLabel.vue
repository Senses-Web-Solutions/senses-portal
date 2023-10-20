<template>
    <label class="block font-medium text-zinc-500 capitalize">
        <slot></slot>{{required ? ' *' : ''}}
    </label>
</template>
<script>
export default {
     props: {
        required: {
            type: Boolean,
            default: false
        },
     }
};
</script>
