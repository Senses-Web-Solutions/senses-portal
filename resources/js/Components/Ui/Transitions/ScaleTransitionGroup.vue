<template>
    <TransitionGroup
        enter-active-class="transition duration-100 ease-out"
        enter-from-class="scale-0"
        enter-to-class="scale-100"
        leave-active-class="transition duration-75 ease-in"
        leave-from-class="scale-100"
        leave-to-class="scale-0"
    >
        <slot></slot>
    </TransitionGroup>
</template>