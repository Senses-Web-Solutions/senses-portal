<template>
    <div>
        <!-- fixed inset-y-0 right-0  -->
        <section class="flex max-w-full">
            <!-- pl-10 -->
            <transition-group
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="translate-x-full"
                enter-to-class="translate-x-0"
                leave-active-class="transition duration-300 ease-in"
                leave-from-class="translate-x-0 opacity-100"
                leave-to-class="translate-x-full opacity-0"
            >
                <component
                    :is="aside.name"
                    v-for="(aside, asideIndex) in $asides.all"
                    :key="`aside${asideIndex}`"
                    :aside-index="asideIndex"
                    :data="aside.data"
                >
                    <AsideLayout v-bind="$props">
                        <div class="flex items-center justify-center h-full">
                            <div class="space-y-6 text-center">
                                <h2 class="text-6xl">🙃</h2>
                                <h2 class="text-5xl">Oh no!</h2>
                                <div>
                                    <p
                                        class="text-2xl"
                                    >Something went wrong. Don't worry, we're on it.</p>
                                    <p
                                        class="text-zinc-300 transition"
                                    >Has this message been here for a while? Open a livechat and let us know!</p>
                                </div>
                                <!-- Make sure your aside is imported correctly. -->
                            </div>
                        </div>
                    </AsideLayout>
                </component>
            </transition-group>
        </section>
        <transition
            enter-active-class="transition duration-200 ease-in-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-200 ease-in-out delay-100"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="$asides.all.length"
                class="absolute inset-0 bg-black bg-opacity-50 nightwind-prevent"
                @click="$asides.pop()"
            ></div>
        </transition>
    </div>
</template>
<script>
import * as Components from "../index";
import AsideLayout from './AsideLayout.vue';

export default {
    __se_is: 'SensesAside', // here for devtools
    components: {
        ...Components,
        AsideLayout
    },
    watch: {
        '$asides.all' (v) {
            if (v.length) {
                document.body.classList.add("overflow-hidden");
            } else {
                document.body.classList.remove("overflow-hidden");
            }
        }
    }
};
</script>
