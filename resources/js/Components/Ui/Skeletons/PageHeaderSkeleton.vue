<template>
    <section class="w-full px-5 py-4 flex justify-between items-center shadow bg-white sticky top-0 z-40">
        <div class="my-1 animate-pulse">
            <h3 class="text-2xl leading-5 font-medium pb-1">
                <div class="h-6 w-64 bg-zinc-200 rounded"></div>
            </h3>
        </div>
        <ButtonGroup class="animate-pulse">
            <span class="py-2 px-4 rounded-md border shadow-sm font-medium border-transparent bg-zinc-200 hidden md:inline-block">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
            <span class="py-2 px-4 rounded-md border shadow-sm font-medium border-transparent bg-zinc-200 hidden md:inline-block">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
            <span class="py-2 px-4 rounded-md border shadow-sm font-medium border-transparent bg-zinc-200">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
        </ButtonGroup>
    </section>
</template>
<script>
    import ButtonGroup from '../Buttons/ButtonGroup.vue';

    export default {
        components: {ButtonGroup},
    }
</script>
