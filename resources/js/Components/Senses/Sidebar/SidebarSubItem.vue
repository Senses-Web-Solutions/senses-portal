<template>
    <a
        :href="disabled ? '#' : to"
        class="flex items-center py-1 ml-4 text-zinc-700 border-l-2 border-zinc-200 rounded-r-md hover:bg-primary-200 hover:border-primary-500 group text-md"
        :class="{ 'border-primary-700 hover:text-zinc-700 font-semibold dark:border-primary-200': active }"
    >
        <p class="pl-4 text-md">{{ title }}</p>
    </a>
</template>
<script>
export default {
    inject: ['group'],
    props: {
        to: {
            type: String,
            default: '#'
        },
        title: {
            type: String,
            required: true
        },
        active: {
            type: Boolean,
            default: false
        },
        disabled: {
            type: Boolean,
            default: false
        }
    },
    created () {
        this.group.items.push({
            title: this.title,
            to: this.to,
        });
        if (this.active) {
            this.group.active = this.active;
        }
    },
}
</script>
