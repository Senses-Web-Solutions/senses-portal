<template>
    <div class="mx-auto truncate">
        <div class="flex justify-center">
            <div :class="{'py-4': !flush}">
                <span
                    class="mx-auto inline-flex items-center rounded-full bg-zinc-100 px-3 py-0.5 text-center text-zinc-800"
                    :class="{ capitalize: capitalise }"
                >
                    <slot>No {{ item }} Found</slot>
                </span>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        item: {
            type: String,
            default: 'Results',
        },
        capitalise: {
            type: Boolean,
            default: true,
        },
        flush: {
            type: Boolean,
            default: false,
        },
    },
};
</script>
