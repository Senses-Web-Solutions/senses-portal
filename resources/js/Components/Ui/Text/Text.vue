<template>
    <p class="space-x-1" :class="$slots.default && $slots.default.length > 1 ? 'flex' : null">
        <slot></slot>
    </p>
</template>
