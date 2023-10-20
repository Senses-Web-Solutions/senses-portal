<template>
    <div
        class="fixed inset-0 flex items-end justify-center px-4 py-6 pointer-events-none sm:p-6 z-70 overflow-hidden"
    >
        <!-- sm:items-start sm:justify-end -->
        <!--
    Snackbar panel, show/hide based on alert state.

    Entering: "ease-out duration-300 transition"
      From: "translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
      To: "translate-y-0 opacity-100 sm:translate-x-0"
    Leaving: "transition ease-in duration-100"
      From: "opacity-100"
      To: "opacity-0"
        -->
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="translate-y-6 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="translate-y-6 opacity-0"
            mode="out-in"
        >
            <div
                v-if="currentSnackbar"
                :key="currentSnackbar.key"
                class="absolute w-full max-w-md bg-white rounded-lg shadow-2xl pointer-events-auto"
                :class="{
                    'bg-white': !currentSnackbar.type,
                    'bg-info-200 text-info-800': currentSnackbar.type === 'info',
                    'bg-danger-200 text-danger-800': currentSnackbar.type === 'danger',
                    'bg-success-200 text-success-800': currentSnackbar.type === 'success'
                }"
            >
                <div class="overflow-hidden rounded-lg ring-1 ring-black ring-opacity-5">
                    <div class="p-4">
                        <div class="flex items-start gap-4">
                            <div class="w-0 flex-1 pt-0.5">
                                <p class="font-medium leading-5">{{ currentSnackbar.title }}</p>
                                <p
                                    v-if="currentSnackbar.description"
                                    class="mt-1 leading-5 text-zinc-600 text-sm"
                                    :class="{
                                        'text-zinc-600': !currentSnackbar.type,
                                        'text-info-600': currentSnackbar.type === 'info',
                                        'text-danger-600': currentSnackbar.type === 'danger',
                                        'text-success-600': currentSnackbar.type === 'success'
                                    }"
                                >{{ currentSnackbar.description }}</p>
                                <p
                                    v-if="currentSnackbar.postText"
                                    class="mt-1 leading-5 text-zinc-600 text-xs"
                                    :class="{
                                        'text-zinc-600': !currentSnackbar.type,
                                        'text-info-600': currentSnackbar.type === 'info',
                                        'text-danger-600': currentSnackbar.type === 'danger',
                                        'text-success-600': currentSnackbar.type === 'success'
                                    }"
                                >{{ currentSnackbar.postText }}</p>
                            </div>
                            <div class="flex shrink-0 pt-0.5">
                                <button
                                    class="inline-flex  transition duration-150 ease-in-out focus:outline-none "
                                    :class="{
                                        'text-zinc-600 focus:text-zinc-800': !currentSnackbar.type,
                                        'text-info-600 focus:text-info-800': currentSnackbar.type === 'info',
                                        'text-danger-600 focus:text-danger-800': currentSnackbar.type === 'danger',
                                        'text-success-600 focus:text-success-800': currentSnackbar.type === 'success',
                                    }"
                                    @click="$snackbars.remove(0)"
                                >
                                    <XIcon class="w-5 h-5" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>
<script>
import {
    XIcon,
} from '@heroicons/vue/outline';
import { computed } from 'vue';
import snackbars from '../../Support/Snackbars';

export default {
    components: {
        XIcon,
    },
    setup () {
        const currentSnackbar = computed(() => snackbars.snackbars[0] || null);

        return { currentSnackbar }
    },
};
</script>
