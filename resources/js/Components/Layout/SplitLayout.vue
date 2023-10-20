<template>
    <div class="flex flex-wrap md:flex-nowrap">
        <div class="w-full lg:w-1/3">
            <slot name="left"></slot>
        </div>
        <div class="w-full lg:w-2/3">
            <slot name="right"></slot>
        </div>
    </div>
</template>
