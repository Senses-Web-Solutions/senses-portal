<template>
    <InputGroup :label="label" :id="id" :error="error" :name="name">
        <div class="grid rounded-md overflow-hidden border border-zinc-200" :style="'grid-template-columns: repeat(' + this.columnNo + ', minmax(0, 1fr));'">
            <div v-for="colour in colours" :key="colour">
                <div class="col-span-1 flex items-center justify-center w-full h-8 cursor-pointer hover:border-zinc-200 hover:border" :class="['bg-' + colour]" @click="colourSelected(colour)">
                    <CheckIcon class="h-6 w-6" :class="colour.endsWith('-light') || colour.endsWith('-lighter') ? 'text-zinc-700' : 'text-white'" v-if="colour === modelValue" />
                </div>
            </div>
        </div>
    </InputGroup>
</template>
<script>
import InputGroup from "../Inputs/InputGroup.vue";
import { CheckIcon } from "@heroicons/vue/outline";
import Colour from "../../../Enums/Colour";

export default {
    components: {
        InputGroup, CheckIcon
    },
    props: {
        label: {
            type: String,
            required: false,
        },
        modelValue: {
            required: true,
        },
        error: {
            required: false,
            default: null,
        },
        blacklist: { //keywords you don't want colours to include, such as 'darker' or 'red'
            type: Array,
            required: false,
        },
        name: {
            type: String,
            required: true,
        },
        id: {
            type: String,
            required: true,
        },
        columnNo: {
            type: String,
            default: '9',
        },
    },
    data() {
        return {
        }
    },
    emits: ["update:modelValue"],
    methods: {
        colourSelected(colour) {
            this.$emit("update:modelValue", colour);
        }
    },
    computed: {
        colours() {
            if (!this.blacklist) {
                return Object.values(Colour);
            }
            var blacklist = this.blacklist;
            //blacklist = ['dark', 'darker'];
            //colour = 'red-dark';
            return Object.values(Colour).filter(function (colour) {
                    var success = true;
                    Object.values(blacklist).forEach((blacklistItem) => {
                    if (colour.includes(blacklistItem)) {
                        success = false;
                    }
                });
                return success;
            });
        }
    }
};
</script>
