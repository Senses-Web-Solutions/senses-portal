<template>
    <Transition
        enter-active-class="duration-300 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="duration-200 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0">
        <div v-if="$modals.all.length" key="modalBackground" class="fixed z-70 inset-0 bg-zinc-500 bg-opacity-75 transition-opacity"/>
    </Transition>
    <div
        class="fixed z-70 inset-0 flex items-center justify-center"
        :class="$modals.all.length ? '' : 'pointer-events-none'"
        key="modalContainer"
        @mousedown.self="$modals.pop()"
        >
        <TransitionGroup
            enter-active-class="duration-150 ease-out"
            enter-from-class="translate-y-4 opacity-0 sm:translate-y-0 sm:scale-80"
            enter-to-class="translate-y-0 opacity-100 sm:scale-100"
            leave-active-class="duration-200 ease-in"
            leave-from-class="translate-y-0 opacity-100 sm:scale-100"
            leave-to-class="translate-y-4 opacity-0 sm:translate-y-0 sm:scale-80">
            <div
                v-for="(modal, modalIndex) in $modals.all"
                :key="`modal-${modalIndex}`"
                class="relative inline-block overflow-hidden rounded-lg bg-white text-left align-middle shadow-xl transition-all"
                :class="{
                    'p-4 sm:p-6': !modal.data.flush,
                }"
                >
                <div v-if="!modal.data.hideCloseButton" class="absolute top-0 right-0 hidden pt-3 pr-3 sm:block">
                    <button type="button" class="rounded-md bg-white text-zinc-400 hover:bg-zinc-100 hover:text-zinc-500 focus:outline-none focus:ring-1 focus:ring-primary-500 focus:ring-offset-2" @click="$modals.pop()">
                        <XIcon class="w-5 h-5 text-zinc-500" />
                    </button>
                </div>
                <component :is="modal.name" :modal-index="modalIndex" :data="modal.data"></component>
            </div>
        </TransitionGroup>
    </div>
</template>

<script>
import * as Components from '../index';
import modals from '../../Support/Modals';
import { Dialog, DialogOverlay, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'

import {
    XIcon
} from '@heroicons/vue/outline';

export default {
    __se_is: 'SensesModal', // here for devtools
    components: {
        ...Components,
        XIcon,
    },
    data() {
        return {
            open: false,
            modals,
        };
    },
    watch: {
        // eslint-disable-next-line func-names
        'modals.modals': function (v) {
            if (v.length) {
                document.body.classList.add('overflow-hidden');
                this.$nextTick(() => {
                    this.open = true;
                });
            } else {
                document.body.classList.remove('overflow-hidden');
                this.$nextTick(() => {
                    this.open = false;
                });
            }
        },
    },
};
</script>
