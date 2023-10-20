<template>
    <SplitLayout class="pb-8 last:pb-0 space-x-6">
        <template #left>
            <div class="flex items-center font-medium mb-1">
                <div class="text-zinc-500 group-hover:text-teal-500 shrink-0 -ml-1 mr-2 h-6 w-6">
                    <slot name="icon"></slot>
                </div>
                <p class="font-medium text-md capitalize">
                    <slot name="title"></slot>
                </p>
            </div>
            <p class="text-zinc-500">
                <slot name="description"></slot>
            </p>
        </template>
        <template #right>
            <div class="space-y-5">
                <slot></slot>
            </div>
        </template>
    </SplitLayout>
</template>
<script setup>
import SplitLayout from './SplitLayout.vue';
</script>
