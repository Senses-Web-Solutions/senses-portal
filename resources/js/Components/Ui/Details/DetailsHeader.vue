<template>
    <div class="relative items-center py-5 mx-auto text-center">
        <slot name="header">
            <div :class="'bg-' + colour" class="inline-flex items-center justify-center object-cover mx-auto mb-2 rounded-full shadow h-14 w-14">
                <UserIcon class="w-6 h-6" />
            </div>
        </slot>

        <h3 class="text-xl font-medium leading-6">
            <slot name="title" />
        </h3>

        <p v-if="$slots.subtitle" class="text-zinc-700 text-sm-old">
            <slot name="subtitle" />
        </p>

        <div class="absolute top-2 right-2">
            <slot name="action" />
        </div>
    </div>
</template>

<script>
import {
    UserIcon,
} from '@heroicons/vue/outline';

export default {
    components: {
        UserIcon,
    },

    props: {
        colour: {
            type: String,
        }
    },
}

</script>
