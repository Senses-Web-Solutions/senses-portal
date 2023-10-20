<template>
    <InputGroup :label="label" :id="id" :is-valid="isValid(name, error)">
        <div class="flex items-center" :class="{ 'text-zinc-500': disabled }">
            <SeSelectBasic
                :id="id + '_hours'"
                :options="hours"
                :name="name + '_hours'"
                :model-value="hour"
                :disabled="disabled"
                placeholder="Hrs"
                class="w-18 mr-2"
                :is-valid="isValid(name, error)"
                :error="error"
                @update:modelValue="calculateTime('hour', $event)" />

            <SeSelectBasic
                :id="id + '_minutes'"
                :options="minutes"
                :name="name + '_minutes'"
                :model-value="minute"
                :disabled="disabled || hour == maxHour"
                placeholder="Mins"
                class="w-18"
                :is-valid="isValid(name, error)"
                :error="error"
                @update:modelValue="calculateTime('minute', $event)" />

            <XCircleIcon
                v-if="clearable"
                class="ml-2 h-5 w-5 cursor-pointer text-zinc-500"
                @click="clear" />
        </div>
    </InputGroup>
</template>
<script>
import { format, parse } from 'date-fns';
import { XCircleIcon } from '@heroicons/vue/outline';
import SeSelectBasic from './SeSelectBasic.vue';
import InputGroup from './InputGroup.vue';
import valid from '../../../Mixins/Valid';

export default {
    components: {
        SeSelectBasic,
        InputGroup,
        XCircleIcon,
    },
    mixins: [valid],
    props: {
        label: {
            type: String,
            required: false,
        },
        modelValue: {
            required: true,
        },
        name: {
            type: String,
            required: true,
        },
        minHour: {
            type: Number,
            default: 0,
        },
        maxHour:{
            type:Number,
            default: 24
        },
        id: {
            type: String,
            required: true,
        },
        seconds: {
            type: Boolean,
            default: true,
        },
        error: {
            type: Object,
            required: false,
        },
        disabled: {
            type: Boolean,
            default: false
        },
        clearable: {
            type: Boolean,
            default: false,
        },
        width: {
            type: String,
            default: 'w-20',
        },
    },
    emits: ['update:modelValue'],
    computed: {
        hours() {
            let hours = [];

            for (let i = this.minHour; i < 24 + this.minHour; ++i) {
                if (i > ((this.maxHour > this.minHour) ? this.maxHour : 24 + this.maxHour)) {
                    continue;
                }

                if (i >= 24) {
                    var hour = i - 24;
                } else {
                    var hour = i;
                }

                if (hour.toString().length == 1) {
                    hour = '0' + hour.toString();
                }

                hours.push({ id: hour.toString(), title: hour.toString() });
            }

            return hours;
        },
        minutes() {
            let minutes = [
                { id: '00', title: '00' },
                { id: '15', title: '15' },
                { id: '30', title: '30' },
                { id: '45', title: '45' }
            ];

            for (let minute = 1; minute < 60; ++minute) {
                if (minute.toString().length == 1) {
                    minute = '0' + minute.toString();
                }

                minutes.push({
                    id: minute.toString(),
                    title: minute.toString(),
                });
            }

            return minutes;
        },

        // isValid() {
        //     if (this.error && this.error.errors) {
        //         return !this.error.errors[this.name];
        //     }
        //     return true;
        // },
    },
    data() {
        return {
            hour: null,
            minute: null,
        };
    },
    created() {
        if (this.modelValue) {
            const parts = this.modelValue.split(':');
            if (parts[0]) {
                this.hour = parts[0];
            }
            if (parts[1]) {
                this.minute = parts[1];
            }
        }
    },
    methods: {
        calculateTime(type, value) {
            if (type === 'hour') {
                this.hour = value;
            } else if (type === 'minute') {
                this.minute = value;
            }

            if (!this.hour) {
                this.$emit('update:modelValue', null);
                return null;
            }

            if (!this.minute) {
                this.$emit('update:modelValue', null);
                return null;
            }

            const seconds = this.seconds ? ':00' : '';
            const time = this.hour + ':' + this.minute + seconds;
            this.$emit('update:modelValue', time);
        },
        clear() {
            this.$emit('update:modelValue', null);
        },
    },
    watch: {
        modelValue(newValue) {
            if (newValue) {
                this.hour = format(parse(this.modelValue, 'HH:mm:ss', new Date()), 'HH');
                this.minute = format(parse(this.modelValue, 'HH:mm:ss', new Date()), 'mm');

                let maxHourString = this.maxHour.toString();
                if (maxHourString.length == 1) {
                    maxHourString = '0' + maxHourString;
                }

                if (this.hour == maxHourString) {
                    this.minute = '00';
                };
            } else {
                this.hour = '00';
                this.minute = '00';
            }
        },
    },
};
</script>
