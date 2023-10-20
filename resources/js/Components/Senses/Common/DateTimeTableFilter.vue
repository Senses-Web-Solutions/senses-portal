<template>
    <TableFilterSubTypeSelect v-model="formFilter.sub_type" :filter="this.field.filter"></TableFilterSubTypeSelect>

    <template v-if="formFilter.sub_type == 'exact'">
        <SeDateTimePicker 
            v-model="formFilter.exactDate" 
            :name="'filter['+field.key+']'" 
            :id="'filter['+field.key+']'" 
            label="Value"/>
    </template>
    <template v-if="formFilter.sub_type == 'between'">
            <SeDateTimeRangePicker v-model="formFilter.betweenDate" :name="'filter['+field.key+']'" :id="'filter['+field.key+']'" label="Value"></SeDateTimeRangePicker>
    </template>

    <div class="flex justify-end">
        <PrimaryButton @click="applyFilter">Add Filter</PrimaryButton>
    </div>
</template>
<script>
import SeDateTimeRangePicker from '../../Ui/Inputs/SeDateTimeRangePicker.vue';
import SeDateTimePicker from '../../Ui/Inputs/SeDateTimePicker.vue';
import TableFilterSubTypeSelect from '../Tables/TableFilterSubTypeSelect.vue';
import PrimaryButton from '../../Ui/Buttons/PrimaryButton.vue';
export default {
    components:{ SeDateTimeRangePicker, TableFilterSubTypeSelect, SeDateTimePicker, PrimaryButton },
    emits:['update:field', 'applyFilter'],
    props:{
        field:{
            type:Object,
            required:true
        }
    },
    watch:{
        field(newVal, oldVal) {
            this.clearFormFilter();
        }
    },
    data() {
        return {
            formFilter:{
                sub_type:null,
                value:null,
                exactDate:null,

                betweenDate: {
                    start_date:null,
                    end_date:null,
                }
            }
        }
    },

    created() {
        this.clearFormFilter();
    },

    methods:{
        applyFilter() {
            if(this.formFilter.sub_type == 'between') {
                this.formFilter.value = [this.formFilter.betweenDate.start_date, this.formFilter.betweenDate.end_date].join(',');
            }
            else {
                this.formFilter.value = this.formFilter.exactDate;
            }
            this.$emit('applyFilter', this.formFilter);
        },

        clearFormFilter() {
            this.formFilter.value = null;
            this.formFilter.sub_type = null;
            if(this.field && this.field.filter.sub_types && Object.keys(this.field.filter.sub_types).length > 0) {
                this.formFilter.sub_type = Object.keys(this.field.filter.sub_types)[0];
            }
        }
    }
}
</script>