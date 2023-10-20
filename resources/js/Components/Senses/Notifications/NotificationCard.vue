<template>
    <div>
        <transition
        enter-active-class="transition duration-300 ease-out origin-bottom"
        enter-from-class="opacity-0 scale-110 -translate-y-3"
        enter-to-class="opacity-100"
        leave-active-class="transition duration-200 ease-in origin-bottom"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0 scale-110 -translate-y-3">
            <Card class="transform transition-all duration-500">
                <div class="flex justify-between items-center mb-4">
                    <slot name="title" />
                    <div class="flex space-x-3">
                        <button
                            class="text-sm text-zinc-500 hover:text-purple-700 p-1"
                            @click="$emit('mark-all-read')"
                        >Mark all as read</button>
                    </div>
                </div>

                <slot />
            </Card>
        </transition>
    </div>
</template>

<script>
import Card from '../../Ui/Cards/Card.vue';

export default {
    components: {
        Card
    },
    props: {
        notifications: {
            type: Array,
            required: false,
            default: () => [],
        },
        type: {
            type: String,
            default: 'notification'
        }
    },
    emits: ['remove', 'mark-all-read'],
}
</script>
