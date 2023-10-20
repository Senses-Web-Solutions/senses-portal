<template>
    <Tooltip>
        <template #content v-if="toDo.important">Remove importance</template>
        <template #content v-else>Mark as important</template>
        <ToDoButton v-if="toDo" :class="classes" @click.prevent.stop="toggleImportant">
            <template #icon>
                <ExclamationIcon class="h-4 w-4"/>
            </template>
        </ToDoButton>
    </Tooltip>
</template>
<script>
// @todo move to Senses/

import ToDoButton from './ToDoButton.vue';
import {ExclamationIcon} from '@heroicons/vue/outline';
import Axios from "axios";
import EventHub from "../../../Support/EventHub";
import Tooltip from '../../Ui/Tooltip.vue';

export default {
    props: {
        toDo: {
            type: Object,
            required: true
        }
    },
    components: {
        ToDoButton,
        Tooltip,
        ExclamationIcon
    },
    computed: {
        classes() {
            let colours = [
                'hover:border-red-400', 'hover:text-red-500', 'focus:outline-none'
            ];
            if (this.toDo.important) {
                colours = colours.concat(['border-red-400', 'text-red-500'])
            } else {
                colours = colours.concat(['text-zinc-300', 'border-zinc-300'])
            }
            return colours;
        }
    },
    methods: {
        toggleImportant() {
            EventHub.emit('loading-to-dos');
            if (this.toDo.important) {
                Axios.post(`/api/v2/to-dos/${this.toDo.id}/important`, {
                    important: false,
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
                Axios.post(`/api/v2/to-dos/${this.toDo.id}/important`, {
                    important: true,
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
