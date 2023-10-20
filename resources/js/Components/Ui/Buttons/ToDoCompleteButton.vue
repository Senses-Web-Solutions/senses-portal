<template>
    <ToDoButton v-if="toDo" :class="classes" @click.prevent.stop="toggleComplete">
        <template #icon>
            <CheckIcon class="h-4 w-4"/>
        </template>
    </ToDoButton>
</template>
<script>
// @todo move to Senses/

import ToDoButton from './ToDoButton.vue';
import {CheckIcon} from '@heroicons/vue/outline';
import Axios from "axios";
import EventHub from "../../../Support/EventHub";
import {format} from "date-fns";

export default {
    props: {
        toDo: {
            type: Object,
            required: true
        }
    },
    components: {
        ToDoButton,
        CheckIcon
    },
    computed: {
        classes() {
            let colours = [
                'hover:border-green-400', 'hover:text-green-500', 'focus:outline-none'
            ];
            if (this.toDo.complete) {
                colours = colours.concat(['border-green-400', 'text-green-500'])
            } else {
                colours = colours.concat(['text-zinc-300', 'border-zinc-300'])
            }
            return colours;
        }
    },
    methods: {
        toggleComplete() {
            EventHub.emit('loading-to-dos');
            if (!this.toDo.complete) {
                Axios.post(`/api/v2/to-dos/${this.toDo.id}/complete`, {
                    completed_at: format(new Date(), 'yyyy-MM-dd HH:mm:ss'),
                })
                    .then(response => {
                        EventHub.emit('to-do-updated', response.data);
                    })
                    .catch(() => {
                        this.$notifications.push({
                            title: 'Error',
                            description: 'There was a problem updating this to do',
                            type: 'danger',
                        });
                    });
            } else {
                Axios.post(`/api/v2/to-dos/${this.toDo.id}/complete`, {
                    completed_at: null,
                })
                    .then(response => {
                        EventHub.emit('to-do-updated', response.data);
                    })
                    .catch(() => {
                        this.$notifications.push({
                            title: 'Error',
                            description: 'There was a problem updating this to do',
                            type: 'danger',
                        });
                    });
            }
        }
    },
}
</script>
