<template>
    <div class="relative">
        <span v-if="!last" class="absolute top-5 -ml-px h-full w-0.5 bg-zinc-100 left-4" aria-hidden="true"></span>
        <div class="flex items-center w-full">
            <div class="!h-8 !w-8 bg-zinc-100 rounded-full flex items-center justify-center z-10 relative">
                <slot name="icon"></slot>
            </div>
            <div class="flex items-center justify-between w-[90%] text-sm">
                <div class="font-medium text-zinc-400 translate-x-3">
                    <slot name="title" />
                </div>
                <div class="font-medium text-zinc-400">{{ formattedDatetime }}</div>
            </div>
        </div>
    </div>
</template>
<script>
import { formatDistanceToNow } from 'date-fns'

export default {
    props: {
        last: {
            type: Boolean,
            default: false
        },
        datetime: {
            type: String,
            required: true
        }
    },
    computed: {
        formattedDatetime() {
            const date = new Date(this.datetime);
            const formattedDate = formatDistanceToNow(date, { addSuffix: true });

            // If formattedDate contains "about ", remove it
            if (formattedDate.includes('about ')) {
                return formattedDate.replace('about ', '');
            }

            return formattedDate;
        }
    }
}
</script>