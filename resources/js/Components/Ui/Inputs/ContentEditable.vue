<template>
    <div :id="field + '-editable'">
        <SeValidation :error="error" :name="field">
            <StrongText v-if="label">{{ label }}</StrongText>
            <div class="flex items-center w-full space-x-2">
                <component :error="error" id="" name="" :class="{'w-full': type === 'textarea' && edit }"
                           placeholder="Enter content here" v-model="proxyData" :is="findComponent">
                    {{ !edit ? proxyData ? proxyData : 'This content is empty' : null }}
                </component>
                <span v-if="edit" class="flex items-center space-x-1">
                    <CheckButton @click="save"/>
                    <XButton @click="cancel"/>
                </span>
                <button v-else @click="edit = true"
                    class="inline-flex items-center text-zinc-400 p-1 border rounded-full shadow-sm hover:shadow-md bg-white focus:outline-none">
                    <PencilAltIcon class="h-4 w-4"/>
                </button>
            </div>
        </SeValidation>
    </div>
</template>
<script>
// todo this input needs renaming, it's not a contenteditable.
//todo selects?
import {PencilAltIcon} from "@heroicons/vue/outline";
import SeColour from "./SeColour.vue";
import SeDateTimePicker from "./SeDateTimePicker.vue";
import SeDatePicker from "./SeDatePicker.vue";
import SeTextArea from "./SeTextArea.vue";
import SeInput from "./SeInput.vue";
import Colour from "../Colour.vue";
import Text from "../Text/Text.vue";
import EditButton from "../Buttons/EditButton.vue";
import CheckButton from "../Buttons/CheckButton.vue";
import SeValidation from "./SeValidation.vue";
import XButton from "../Buttons/XButton.vue";
import StrongText from "../Text/StrongText.vue";

export default {
    components: {EditButton, CheckButton, SeValidation, XButton, StrongText, PencilAltIcon},
    props: {
        data: {
            default: null
        },
        label: {
            type: String,
            default: null
        },
        field: {
            type: String,
            required: true
        },
        type: {
            default: "text",
            validator(value) {
                return ["text", "textarea", "date", "datetime", "colour"].includes(value);
            },
        },
        error: {
            default: () => ({})
        }
    },

    emits: ["update:data"],

    data() {
        return {
            edit: false,
            proxyData: this.data,
            oldData: this.data
        }
    },

    methods: {
        save() {
            this.edit = false;
            this.$emit('update:data', this.proxyData);
        },
        cancel() {
            this.proxyData = this.data;
            this.edit = false;
        }
    },

    computed: {
        findComponent() {
            let viewType = 'view';
            if (this.edit) {
                viewType = 'edit';
            }
            let componentTypes = {
                text: {view: Text, edit: SeInput},
                textarea: {view: Text, edit: SeTextArea},
                date: {view: Text, edit: SeDatePicker},
                datetime: {view: Text, edit: SeDateTimePicker},
                colour: {view: Colour, edit: SeColour},
            }
            return componentTypes[this.type][viewType];
        },
    },

    watch: {
        data(newData) {
            this.proxyData = newData;
        },
    }
}

</script>
