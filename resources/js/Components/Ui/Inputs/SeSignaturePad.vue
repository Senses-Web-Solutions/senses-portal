<template>
    <div>
        <SeLabel :required="required">{{ label }}</SeLabel>
        <div class="mt-2" v-if="modelValue == null || typeof modelValue == 'string'">
            <div
                class="mb-3 border border-zinc-300 rounded-md shadow-sm focus:ring-4 focus:ring-purple-200 focus:border-purple-500"
                style="height: 250px"
                ref="wrapper"
            >
                <canvas ref="signaturePad"/>
            </div>
            <SecondaryButton @click="undo">Undo</SecondaryButton>
        </div>
        <div v-else-if="modelValue != null && Number.isInteger(modelValue)">
            <div class="border rounded-md border-zinc-300 p-4 h-[114px] flex justify-between items-end mt-2">
                <File :fileId="modelValue" :deletable="false" condensed />
                <DangerButton size="xs" @click="clearImage">
                    <XIcon class="w-5 h-5" />
                </DangerButton>
            </div>
        </div>
    </div>
</template>
<script>
import DangerButton from '../Buttons/DangerButton.vue';
import File from '../../Senses/Files/File.vue';
import SignaturePad from 'signature_pad';
import SecondaryButton from '../../Ui/Buttons/SecondaryButton.vue';
import SeLabel from '../../Ui/Inputs/SeLabel.vue';

import { XIcon } from "@heroicons/vue/outline";

export default {
    components: {
        SecondaryButton,
        SeLabel,
        File,
        DangerButton,
        XIcon
    },
    emits:['update:modelValue'],
    props: {
        modelValue: {
            required: true,
        },
        required: {
            type: Boolean,
            default: false,
        },
        label: {
            type: String,
            default: null,
        },
    },
    data() {
        return {
            signaturePad: null,
        };
    },
    mounted() {
        this.setupSignaturePad();
    },
    methods: {
        setupSignaturePad() {
            if (this.modelValue == null) {
                this.signaturePad = new SignaturePad(this.$refs.signaturePad, {
                    // backgroundColor: 'rgba(0, 0, 255, 0)',
                    penColor: 'rgb(0, 0, 0)',
                    minWidth: 1,
                    maxWidth: 1,
                    onEnd: () => {
                        this.$emit('update:modelValue', this.toDataURL());
                    },
                });
                this.$refs.signaturePad.width = this.$refs.wrapper.offsetWidth;
                this.$refs.signaturePad.height = 250;
            }
        },
        fromDataURL(dataUrl) {
            this.signaturePad.fromDataURL(dataUrl);
        },
        toDataURL(type = 'image/png') {
            return this.signaturePad.toDataURL(type);
        },
        undo() {
            let data = this.signaturePad.toData();
            if (data) {
                data.pop(); // remove the last dot or line
                this.signaturePad.fromData(data);
            }
        },

        clearImage() {
            this.$emit("update:modelValue", null);
        },
    },
    watch: {
        modelValue(newValue, oldValue) {
            if (newValue == null) {
                this.$nextTick(() => {
                    this.setupSignaturePad();
                });
            }
        },
    },
};
</script>
