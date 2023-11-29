<template>
    <TransitionRoot as="template" :show="open">
        <Dialog as="div" static class="fixed inset-0 overflow-y-auto z-[80]" @close="resolve(false)" :open="open">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                    <DialogOverlay class="fixed inset-0 transition-opacity bg-zinc-500 bg-opacity-75" />
                </TransitionChild>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200" leave-from="opacity-100 translate-y-0 sm:scale-100" leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                    <div class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                        <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
                            <button type="button" class="text-zinc-400 bg-white rounded-md hover:text-zinc-500 focus:outline-none focus:ring-1 focus:ring-offset-2 focus:ring-primary-500" @click="resolve(false)">
                                <span class="sr-only">Close</span>
                                <XIcon class="w-6 h-6" aria-hidden="true" />
                            </button>
                        </div>
                        <div class="sm:flex sm:items-start">
                            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-zinc-100 rounded-full shrink-0 sm:mx-0 sm:h-10 sm:w-10">
                                <div :class="iconClass" class="w-5 h-5 transition duration-150 ease-in-out">
                                    <BellIcon v-if="type === 'primary' || type === 'secondary'"></BellIcon>
                                    <ExclamationIcon v-if="type === 'warning'"></ExclamationIcon>
                                    <XIcon v-if="type === 'danger'"></XIcon>
                                    <InformationCircleIcon v-if="type === 'info'"></InformationCircleIcon>
                                    <CheckIcon v-if="type === 'success'"></CheckIcon>
                                </div>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <DialogTitle as="h3" class="text-xl font-medium leading-6" data-dusk="dialog-title">
                                    <slot name="title"></slot>
                                </DialogTitle>
                                <div class="mt-2">
                                    <p class="text-zinc-500" data-dusk="dialog-subtitle">
                                        <slot name="subtitle"></slot>
                                    </p>
                                </div>
                                <slot name="command"></slot>
                            </div>
                        </div>
                        <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                            <button :class="buttonClass" type="button" class="inline-flex justify-center w-full px-4 py-2 text-lg font-medium text-white border border-transparent rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-offset-2 sm:ml-3 sm:w-auto" @click="resolve(true)" data-dusk="dialog-action">
                                <slot name="button"></slot>
                            </button>
                            <button v-if="!hideCancel" type="button" class="inline-flex justify-center w-full px-4 py-2 mt-3 text-lg font-medium text-zinc-700 bg-white border border-zinc-300 rounded-md shadow-sm hover:text-zinc-500 focus:outline-none focus:ring-1 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:w-auto" @click="resolve(false)" data-dusk="dialog-cancel">
                                Cancel
                            </button>
                        </div>
                    </div>
                </TransitionChild>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script>
import {XIcon} from '@heroicons/vue/solid';
import {ExclamationIcon} from '@heroicons/vue/solid';
import {InformationCircleIcon} from '@heroicons/vue/solid';
import {CheckIcon} from '@heroicons/vue/solid';
import {BellIcon} from '@heroicons/vue/solid';
import { Dialog, DialogOverlay, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'

export default {
    components: {
        XIcon,
        ExclamationIcon,
        InformationCircleIcon,
        CheckIcon,
        BellIcon,
        Dialog,
        DialogOverlay,
        DialogTitle,
        TransitionChild,
        TransitionRoot,
    },
    props: {
        open: {
            type: Boolean,
            default: false
        },
        hideCancel: {
            type: Boolean,
            default: false
        },
        type: {
            type: String,
            default: 'info',
            validator(value) {
                return ['info', 'success', 'primary', 'warning', 'secondary', 'danger'].includes(value);
            }
        },
        resolve: {
            type: Function
        }
    },
    computed: {
        iconClass() {
            var classes = {
                'info': 'text-zinc-500',
                'success': 'text-green-500',
                'primary': 'text-primary-500',
                'warning': 'text-yellow-500',
                'secondary': 'text-secondary-500',
                'danger': 'text-red-500',
            };
            return classes[this.type];
        },
        buttonClass() {
            var classes = {
                'info': 'bg-zinc-500 focus:ring-zinc-500',
                'success': 'bg-green-500 focus:ring-green-500',
                'primary': 'bg-primary-500 focus:ring-primary-500',
                'warning': 'bg-yellow-500 focus:ring-yellow-500',
                'secondary': 'bg-secondary-500 focus:ring-secondary-500',
                'danger': 'bg-red-500 focus:ring-red-500',
            };
            return classes[this.type];
        },
    }
}
</script>
