<template>
<div>
    <div v-if="editing" class="space-y-2 mb-4 text-md">
        <div v-for="(key, index) in orderedModelValueKeys" :key="index" class="space-y-2">
            <p><span class="inline-block w-60">{{ titleText(key) }}:</span> {{  modelValue[key] }}</p>
        </div>
    </div>
    <FieldGroup v-model="proxyModel" v-model:fields="fields" />
    <div class="flex justify-end">
        <SecondaryButton size="xxs" class="mt-2" @click="resetQuestions">Reset Question</SecondaryButton>
    </div>
</div>
</template>
<script>
import Tooltip from '../../Ui/Tooltip.vue';
import SeInput from '../../Ui/Inputs/SeInput.vue';
import axios from 'axios';
import FieldGroup from '../../Ui/FieldGroup.vue';
import SecondaryButton from "../../Ui/Buttons/SecondaryButton.vue";

export default {
    components: {
        Tooltip,
        SeInput,
        FieldGroup,
        SecondaryButton,
    },
    emits:['update:modelValue', 'update:model-value'],
    props: {
        startingQuestion:{
            type:String,
            required:true,
        },
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

    watch:{
        proxyModel:{
            deep: true,
            handler(val) {

                //todo determine position of changed key
                //if other keys exist, wipe

                let keys = Object.keys(val);
                if(keys.length == 0) {
                    return;
                }

                let key = keys[keys.length - 1];
                if(this.proxyModel[key].next_question_id && !this.loadedQuestions.includes(this.proxyModel[key].next_question_id))
                {
                    this.lockQuestion(key);
                    this.loadQuestion(this.proxyModel[key].next_question_id);
                }
                else if(!this.proxyModel[key].next_question_id) {
                    this.$emit('update:modelValue', this.keyedProxyModel); //emit when final answer selected.
                }
            }
        }
    },
    computed:{
        orderedModelValueKeys() {
            if(!this.modelValue) {
                return {};
            }

            let ordered = [];
            let positions = ['fault-type', 'problem', 'cause','remedy']; //this same thing is happening in SetPCRFieldsOnTask.php

            Object.keys(this.modelValue).forEach((key) => {
                let pos = positions.findIndex((element) => key.includes(element));
                ordered.splice(pos, 0, key);
            });

            return ordered;
        },

        keyedProxyModel() {
            //ensure the model we emit back is in the correct order.
            let keyedProxyModel = {};
            this.keyOrder.forEach(key => {
                if(this.proxyModel[key]) {
                    keyedProxyModel[key] = this.proxyModel[key].answer ?? this.proxyModel[key];
                }
            });
            return keyedProxyModel;
        }
    },
    data() {
        return {
            //this select has a lot of stuff to try and get round the fact data may not be in the correct order, however right now its create only and replies on the
            //fact things are filled out from the start in the right order (and not from the database)
            proxyModel:{},
            fields:[],
            keyOrder:[],
            editing:false,
            loadedQuestions:[],
        }
    },

    mounted() {

        //todo implement ordering on edit
        if(this.modelValue) {
            this.editing = true;
            // Object.keys(this.modelValue).forEach(key => {
            //     this.proxyModel[key] = this.modelValue[key];
            // }); //todo this maybe a shallow copy
        }

        //todo load from data if we have data.
        this.loadQuestion(this.startingQuestion);

        //if modelValue object has key/values load this
        //else load starting question
    },  
    methods:{

        findField(id) {
            return this.fields.find(field => {
                return field.key == id;  
            });
        },

        showQuestion(val) {
            let field = this.findField(val);
            if(field) {
                field['disabled'] = false;
            }
        },

        lockQuestion(val) {
            let field = this.findField(val);
            if(field) {
                field['disabled'] = true;
            }
        },

        resetQuestions() {
            this.fields = [];
            this.loadedQuestions = [];
            this.keyOrder = [];
            this.proxyModel = {};
            this.loadQuestion(this.startingQuestion);
        },

        loadQuestion(id) {
            //ajax call to find question based on uuid or id
           
            axios.get('/api/v2/cascading-questions/' + id)
            .then((response) => {
                this.keyOrder.push(response.data.slug);
                this.loadedQuestions.push(id);
                this.addQuestion(response.data);
            });
        },

        addQuestion(cascadingQuestion) {
            if(cascadingQuestion.response_type == 'select') {
                this.fields.push({
                    key: cascadingQuestion.slug, 
                    label: cascadingQuestion.title,
                    type: "select-search", 
                    textField:"answer",
                    url: "/api/v2/cascading-answers?&filter[question_id_exact]="+ cascadingQuestion.id +"&format=select-search"
                });
            }
        },

        titleText(title) {
            return title.replaceAll('-', ' ');
        }
    }
}
</script>