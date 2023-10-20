<template>
    <SecondaryButton @click="getSignedUrl">
            <DownloadIcon v-if="type.includes('productivity')" class="h-4 w-4"/>
        <slot></slot>
    </SecondaryButton>
</template>
<script>
// @todo move to Senses/
import {DownloadIcon} from "@heroicons/vue/outline";
import SecondaryButton from '../../Ui/Buttons/SecondaryButton.vue';
import axios from 'axios';

export default {
    components: { SecondaryButton, DownloadIcon },
    props: {
        type: {
            type: String,
            required: true,
        },
        data: {
            type: Object,
            default: () => ({}),
        },
        blockData: {
            type: Object,
            default: () => ({}),
        },
    },
    data() {
        return {
            user: window.user,
        };
    },
    methods: {
        getSignedUrl() {
            this.printLoading = true;
            axios
                .get(this.url)
                .then((response) => {
                    this.printReportLayout(response.data);
                    this.printLoading = false;
                })
                .catch((error) => {
                    this.error = error;
                    this.$emit('error', error.response.data);
                    this.$notifications.push({
                        title: 'Print failed',
                        description: error.response.data.message,
                        type: 'danger',
                    });
                    this.printLoading = false;
                });
        },
        printReportLayout(url) {
            axios
                .get(url)
                .then((response) => {
                    this.$notifications.push({
                        title: 'Print queued',
                        description: response.data.message,
                        type: 'success',
                    });
                })
                .catch((error) => {
                    this.error = error;
                    this.$emit('error', error.response.data);
                    this.$notifications.push({
                        title: 'Print failed',
                        description: error.response.data.message,
                        type: 'danger',
                    });
                });
        },
    },
    computed: {
        url(){
            var url = `/api/v2/report-layouts/all/${this.type}/signed-url`;
            var params = [];
            if(this.type == 'user-productivity'){
                params.push(`params[user]=${this.blockData[0].id}`);
            }
            if(Object.keys(this.data).includes('date_type') && this.data.date_type){
                params.push(`params[dateType]=${this.data.date_type}`);
            }
            if(Object.keys(this.data).includes('start_date') && this.data.start_date){
                params.push(`params[startDate]=${this.data.start_date}`);
            }
            if(Object.keys(this.data).includes('end_date') && this.data.end_date){
                params.push(`params[endDate]=${this.data.end_date}`);
            }
            if(Object.keys(this.data).includes('date') && this.data.date){
                params.push(`params[date]=${this.data.date}`);
            }
            if(Object.keys(this.data).includes('depots') && this.data.depots){
                params.push(`params[depots]=${this.data.depots}`);
            }
            if (Object.keys(this.data).includes('user_id') && this.data.user_id) {
                params.push(`params[user_id]=${this.data.user_id}`);
            }
            if (Object.keys(this.data).includes('time_format') && this.data.time_format) {
                params.push(`params[timeFormat]=${this.data.time_format}`);
            }

            var paramString = params.join('&');

            return paramString == '' ? url : url + '?' + paramString;
        }
    },
};
</script>
