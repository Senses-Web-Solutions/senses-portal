<template>
    <QuickGlanceItemLayout>
        <StrongText>{{ title }}:</StrongText>
        <div class="flex space-x-2 justify-between">
            <!-- <template v-if="!showReadableDuration">
                <Popover>
                    <Badge v-if="!date" type="gray" class="truncate"> N/A </Badge>
                    <WarningBadge class="truncate" v-else-if="!completed_at && slaStatus(due_date) != 'overdue'"> Due in {{ readableDuration }} </WarningBadge>
                    <DangerBadge class="truncate" v-else-if="!completed_at && slaStatus(due_date) == 'overdue'"> Overdue by {{ readableDuration }} </DangerBadge>
                    <DangerBadge class="truncate" v-else-if="completed_at && slaStatus(due_date, completed_at) == 'overdue'"> {{ formatDateTime(date) }} </DangerBadge>
                    <SuccessBadge class="truncate" v-else-if="completed_at && slaStatus(due_date, completed_at) != 'overdue'"> {{ formatDateTime(date) }} </SuccessBadge>

                    <template #content>
                        <div>
                            <StrongText>{{ title }}</StrongText>
                            <Text>{{ formatDateTime(date, 'do MMMM y HH:mm:ss') }}</Text>
                        </div>
                    </template>
                </Popover>
            </template> -->
            <Badge v-if="!date" type="gray" class="truncate"> N/A </Badge>
            <WarningBadge class="truncate" v-else-if="!completed_at && slaStatus(due_date) != 'overdue'"> {{ formatDateTime(date) }} </WarningBadge>
            <DangerBadge class="truncate" v-else-if="!completed_at && slaStatus(due_date) == 'overdue'"> {{ formatDateTime(date) }} </DangerBadge>
            <DangerBadge class="truncate" v-else-if="completed_at && slaStatus(due_date, completed_at) == 'overdue'"> {{ formatDateTime(date) }} </DangerBadge>
            <SuccessBadge class="truncate" v-else-if="completed_at && slaStatus(due_date, completed_at) != 'overdue'"> {{ formatDateTime(date) }} </SuccessBadge>
        </div>
    </QuickGlanceItemLayout>
</template>

<script>
    import PopoverModelInfo from '../Ui/PopoverModelInfo.vue';
    import Text from '../Ui/Text/Text.vue';
    import DangerBadge from '../Ui/Badges/DangerBadge.vue';
    import WarningBadge from '../Ui/Badges/WarningBadge.vue';
    import Badge from '../Ui/Badges/Badge.vue';
    import SuccessBadge from '../Ui/Badges/SuccessBadge.vue';
    import Popover from '../Ui/Popover.vue';
    import StrongText from '../Ui/Text/StrongText.vue';
    import QuickGlanceItemLayout from './QuickGlanceItemLayout.vue';
    import formatDateTime from '../../Filters/FormatDateTime';
    import { addHours } from 'date-fns';

    import { getBackendClientConfig } from '../../Support/client';

    export default {
        components: {
            PopoverModelInfo,
            Text,
            DangerBadge,
            WarningBadge,
            SuccessBadge,
            Badge,
            Popover,
            StrongText,
            QuickGlanceItemLayout
        },

        data() {
            return {
                readableDuration: '',
                showReadableDuration: true,
            }
        },

        props: {
            title: {
                type: String,
                required: true
            },

            date: {
                required: true
            },

            due_date: {
                required: true
            },

            completed_at: {
                required: true
            }
        },

        methods: {
            formatDateTime,

            slaStatus(due_date, completed_at) {
                var due_date = new Date(due_date);
                var now = completed_at ? new Date(completed_at) : new Date();

                var due_date_time = due_date.getTime();
                var now_time = now.getTime();

                if (now_time >= due_date_time) {
                    return "overdue";
                }

                // if (addHours(now, 6).getTime() > due_date.getTime()){
                //     return "close";
                // }

                return false;
            },

            hrDuration(sla_date) {
                let now = new Date();
                var date = new Date(sla_date);

                let time = Math.abs(Math.floor((date.getTime() - now.getTime()) / 60000));

                if (time === 0) {
                    return "<1 Min";
                }

                const days = Math.floor(time / 1440);
                const hours = Math.floor(time / 60);
                const mins = time % 60;

                const dayString = days > 1 ? ' Days' : ' Day';
                const hourString = hours > 1 ? ' Hours' : ' Hour';
                const minString = mins > 1 ? ' Mins' : ' Min';

                if (days >= 1) {
                    return `${days} ${dayString}`;
                }

                let string = hours ? `${hours} ${hourString} ` : "";
                string = mins ? `${string}${mins} ${minString}` : string;

                return string;
            },

            diffSlaToNow(sla_date){
                var date = new Date(sla_date);
                var now = new Date();

                var minutes = Math.abs(Math.floor((date.getTime() - now.getTime()) / 60000));
                var hours = Math.abs(Math.ceil(minutes / 60));

                if (minutes <= 90) {
                    return minutes + " Minutes";
                }

                if (hours >= 1) {
                    return hours + " Hours";
                }

                return minutes + " Minutes";
            }
        },

        mounted() {
            if (getBackendClientConfig('timeline_date_mode') == 'date') {
                this.showReadableDuration = false;
            }

            this.readableDuration = this.hrDuration(this.date);

            this.interval = setInterval(() => {
                this.readableDuration = this.hrDuration(this.date);
            }, 500);
        },

        destroyed() {
            clearInterval(this.interval);
        }
    }
</script>
