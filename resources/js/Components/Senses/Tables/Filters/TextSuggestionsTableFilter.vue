<template>
    
    <div class="flex items-center justify-between">
        <SeLabel :for="`filter[${id}]`">Filter</SeLabel>
        <SecondaryButton size="xs" class="text-sm" @click="advanced = !advanced">Adv</SecondaryButton>
    </div>

    <template v-if="advanced">
        <SeInput
            :id="`filter[${id}]`"
            :name="`filter[${id}]`"
            v-model="proxyModel"
            placeholder="Search"
        ></SeInput>
    </template>
    <template v-else>
        <div class="max-h-44 overflow-hidden overflow-y-scroll space-y-2 pr-2 py-2" v-if="!loading && Object.keys(options).length > 0">
            <template v-for="(value, option) in options">
                <SeCheckbox 
                :id="option" 
                :name="option" 
                :label="truncate(option)" 
                class="w-full justify-between" 
                :model-value="value" 
                @update:modelValue="selectOption(option, $event)"                         
                @click.stop="() => { }"/>
            </template>
        </div>
        <LoadingIcon class="w-6 h-6 mx-auto my-8 text-primary" v-if="loading"></LoadingIcon>
    </template>
</template>
<script>
import SeInput from '../../../Ui/Inputs/SeInput.vue';
import axios from 'axios';
import SeCheckbox from '../../../Ui/Inputs/SeCheckbox.vue';
import SecondaryButton from '../../../Ui/Buttons/SecondaryButton.vue';
import SeLabel from '../../../Ui/Inputs/SeLabel.vue';
import LoadingIcon from '../../../Ui/LoadingIcon.vue';

export default {
    components: { SeInput, SeCheckbox, SecondaryButton, SeLabel, LoadingIcon },
    props: {
        id: {
            type: String,
        },
        modelValue: {
            type: String,
        },
        url:{
            type:String,
        }
    },
    data() {
        return {
            options:{},
            advanced:false,
            loading:false,
        }
    },
    computed:{
        optionsUrl() {
            if(this.url.includes('?')) {
                return this.url + '&group_by=' + this.id;
            }
            return this.url + '?group_by=' + this.id;
        },
        proxyModel: {
            get() {
                return this.modelValue;
            },

            set(val) {
                // console.log(val);
                this.$emit('update:modelValue', val);
            },
        },
    },
    emits: ['update:modelValue'],
    mounted() {
        this.loadOptions();
    },
    methods:{
        loadOptions() {
            this.loading = true;
            axios.get(this.optionsUrl)
            .then((response) => {
                let value = this.proxyModel ? this.proxyModel.split(',') : [];
                let options = {};
                response.data.forEach(option => {
                    options[option] = value.includes(option);
                });
                this.options = options;
                this.loading = false;
            })
            .catch((error) => {
                this.loading = false;
            });
        },

        selectOption(option, event) {
            if(event) {
                this.addOption(option);
            }
            else {
                this.removeOption(option);
            }
        },

        truncate(str) {
            const limit = 56;
            if(str.length <= limit) {
                return str;
            }
            return str.substring(0, limit) + '...';
        },

        addOption(option) {
            let value = this.proxyModel ? this.proxyModel.split(',') : [];

            if(value[option]) {
                return;
            }

            value.push(option);

            this.proxyModel = value.join(',');
        },

        removeOption(option) {
            let value = this.proxyModel ? this.proxyModel.split(',') : [];
            if(!value.includes(option)) {
                return;
            }

            let index = value.indexOf(option);
            value.splice(index, 1);

            this.proxyModel = value.join(',');
        }
    }
};
</script>