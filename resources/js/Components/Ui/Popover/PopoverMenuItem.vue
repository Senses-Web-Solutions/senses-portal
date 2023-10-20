<template>
    <button
        class="text-left transition"
        :class="[
            active ? 'border-primary-600 ring-2 ring-primary-600 ' : '',
            'group flex w-full items-center py-2 px-3 text-zinc-700',
        ]"
    >
        <slot :active="active" />
        <svg
            v-if="active"
                                class="h-5 w-5 text-primary-600 absolute top-2 right-2 shrink-0"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 20 20"
            fill="#72569D"
            aria-hidden="true"
        >
            <path
                fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                clip-rule="evenodd"
            ></path>
        </svg>
    </button>
</template>
<script>
export default {
    props: {
        active: {
            type: Boolean,
        },
    },
    setup(props) {
        const colourIsHex = props.dotColour
            ? /^#[0-9A-F]{6}$/i.test(props.dotColour)
            : null;

        return { colourIsHex };
    },
};
</script>
