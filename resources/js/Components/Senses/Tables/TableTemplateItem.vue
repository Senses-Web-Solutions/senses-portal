<template>
    <RadioGroupOption v-slot="{ active, checked }" as="template">
        <div class="relative flex cursor-pointer rounded-lg border border-zinc-200 px-5 py-4 hover:bg-zinc-50 focus:outline-none" :class="[
                active
                    ? 'ring-2 ring-white ring-opacity-60 ring-offset-2 ring-offset-primary-300'
                    : '',
                checked ? 'border-primary-600' : '',
            ]">
            <div class="flex w-full items-center justify-between">
                <div class="pr-5">
                    <RadioGroupLabel as="p" class="font-medium text-zinc-900">
                        <slot name="title" />
                    </RadioGroupLabel>
                    <RadioGroupDescription as="span" class="inline text-zinc-500">
                        <slot name="subtitle" />
                    </RadioGroupDescription>
                </div>
                <div class="flex items-center space-x-2">
                    <TrashIcon v-if="!checked && title != 'Default'" class="h-4 w-4 text-zinc-600 hover:text-red-600" @click.prevent="deleteItem" />
                    <div v-show="checked" class="flex-shrink-0 text-white">
                        <CheckIcon class="h-6 w-6 text-primary-600" />
                    </div>
                </div>
            </div>
        </div>
    </RadioGroupOption>
</template>

<script>
import {
    RadioGroupDescription,
    RadioGroupOption,
    RadioGroupLabel,
} from '@headlessui/vue';
import {
    TrashIcon,
    CheckIcon
} from '@heroicons/vue/outline';

export default {
    props: {
        title: {
            required: true,
            type: String,
        },
    },
    components: {
        RadioGroupDescription,
        RadioGroupOption,
        RadioGroupLabel,
        TrashIcon,
        CheckIcon,
    },

    emits: ['delete:template'],

    methods: {
        async deleteItem() {
            const confirmed = await this.$dialogs.confirm(
                'Delete Table Template',
                "Are you sure you want to delete the '" +
                this.title +
                "' template for this table?"
            );

            if (confirmed) {
                this.$emit('delete:template', this.title);
            }
        },
    },
};
</script>
