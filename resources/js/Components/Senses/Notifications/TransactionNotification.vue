<template>
    <div class="relative flex flex-col space-y-2 p-5 border-b border-zinc-200">
        <div class="flex justify-between">
            <h3 class="font-medium text-zinc-700">{{ transaction.message }}</h3>
            <div class="flex items-center space-x-3">
                <SecondaryButton size="xxs">
                    <XIcon class="transition-all h-4 w-4 cursor-pointer text-zinc-500" @click="$emit('remove')" />
                </SecondaryButton>
            </div>
        </div>
        <div class="flex justify-between items-center">

            <template v-if="transaction.status != 'pending'">
                <span class="h-2 rounded-md bg-zinc-100 w-[80%]">
                    <span class="h-2 rounded-md block" :class="percentage >= 100 ? 'bg-green-500' : 'bg-primary-500'" :style="{
                        width: percentage + '%'
                    }"></span>
                </span>

                <p v-if="transaction.progress !== transaction.progress_total" class="text-sm text-zinc-500 ml-2">{{ percentage >= 100 ? 100 : percentage }}%</p>
            </template>
            <p v-else-if="transaction.type == 'report-export'">
                Your report is in the queue and will be processed shortly.
            </p>
            <p v-else-if="transaction.type == 'table-export'">
                Your table export is in the queue and will be processed shortly.
            </p>
        </div>
    </div>
</template>
<script>
import { XIcon } from "@heroicons/vue/solid";
import SecondaryButton from '../../Ui/Buttons/SecondaryButton.vue';

export default {
    components: {
        XIcon,
        SecondaryButton
    },
    props: {
        transaction: {
            type: Object,
            required: true
        }
    },
    emits: ['remove'],
    computed: {
        percentage() {
            return Math.round(((this.transaction.progress / this.transaction.progress_total) * 100));
        }
    }
}
</script>
