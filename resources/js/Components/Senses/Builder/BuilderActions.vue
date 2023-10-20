<template>
    <EditButton
        :id="id"
        :data="data"
        :disabled="!user().can('update-report-layout')"
        :link="`/report-layouts/${id}/update`"
        model="report-layout" />
    <!-- <PrimaryButton v-if="data.slug === 'user-productivity'" @click="getSignedUrl('summary')">
        Export To PDF
    </PrimaryButton> -->
    <AdditionalOptionsMenu :id="id" :data="data" model="report-layout"></AdditionalOptionsMenu>
</template>

<script>
import axios from "axios";
import EditButton from "../../Ui/Buttons/EditButton.vue";
import AdditionalOptionsMenu from "../../Ui/Menu/AdditionalOptionsMenu.vue";
import user from '../../../Support/user';
import EventHub from "../../../Support/EventHub"

export default {
    components: {
        EditButton,
        AdditionalOptionsMenu,
    },
    props: {
        id: {
            type: Number,
            required: true
        },
        data: {
            type: Object,
            required: true
        },
    },
    data() {
        return {
            user,
            reportLayoutUser: null,
            reportLayoutDate: '',
            reportLayoutDateType: 'normal',
            reportLayoutStartDate: '',
            reportLayoutEndDate: '',
        }
    },
    mounted() {
        // Listen to user
        EventHub.on('report-layout-user-updated', (data) => {
            this.reportLayoutUser = data;
        });

        // Listen for date
        EventHub.on('report-layout-date-updated', (data) => {
            this.reportLayoutDate = data;
        });

        // Listen for date type
        EventHub.on('report-layout-date-type-updated', (data) => {
            this.reportLayoutDateType = data;
        });

        // Listen for startDate
        EventHub.on('report-layout-start-date-updated', (data) => {
            this.reportLayoutStartDate = data;
        });

        // Listen for endDate
        EventHub.on('report-layout-end-date-updated', (data) => {
            this.reportLayoutEndDate = data;
        });

    },
    methods: {
        getSignedUrl(type) {
            const url = this.getUrl(type);
            axios
                .get(url)
                .then((response) => {
                    this.printReportLayout(response.data);
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
        getUrl(type){
            let reportBlockParams = null;
            if (this.data.slug === 'user-productivity') {
                reportBlockParams = {
                    "dateType" : this.reportLayoutDateType,
                    "date" : this.reportLayoutDate,
                    "startDate" : this.reportLayoutStartDate,
                    "endDate" : this.reportLayoutEndDate,
                }
            }
            return `/api/v2/report-layouts/${this.data.id}/${type}/signed-url?params[user]=${this.reportLayoutUser}?params[reportLayout]=${this.data.id}?params[reportBlockParams]=${reportBlockParams}`;
        },
        printReportLayout(url){
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
        }
    }
}
</script>