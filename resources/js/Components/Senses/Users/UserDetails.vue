<template>
    <Details :colour="data.colour">
        <template #title>{{ data.full_name }}</template>
        <template #header>
            <!-- <div v-if="!data.logo_file_id" :class="'bg-' + data.colour" class="mx-auto mb-2 inline-flex h-14 w-14 items-center justify-center rounded-full object-cover shadow">
                <TruckIcon class="h-6 w-6" :class="'text-' + data.text_colour" />
            </div> -->
            <div class="mx-auto mb-2 inline-flex items-center justify-center" v-if="data.image_file_id">
                <File :file-id="data.image_file_id" :deletable="false" size="custom" custom-classes="h-20 w-auto max-w-64 !object-contain" condensed />
            </div>
        </template>
        <template #subtitle>
            <div class="mb-2"> {{ data.job_title }} </div>
            <Tooltip v-if="clockedInAt || clockedOutAt">
                <SuccessBadge v-if="clockedIn"> Clocked In </SuccessBadge>
                <DangerBadge v-else> Clocked Out </DangerBadge>
                <template #content>
                    <div class="text-center" v-if="clockedIn">
                        Clocked in {{ formatDateTime(clockedInAt, 'do MMM') }} <br/> at {{ formatDateTime(clockedInAt, 'HH:mmaaa') }}
                    </div>
                    <div class="text-center" v-else>
                        Last seen {{ formatDateTime(clockedOutAt, 'do MMM') }} <br/> at {{ formatDateTime(clockedOutAt, 'HH:mmaaa') }}
                    </div>
                </template>
            </Tooltip>
        </template>
        <template #action>
            <SecondaryButton v-if="clockedIn && user().can('log-out-user')" :disabled="loading" size="xxs" @click="clockOut">
                <Tooltip>
                    <LogoutIcon v-if="!loading" class="w-5 h-5 text-zinc-500"></LogoutIcon>
                    <LoadingIcon v-else class="w-5 h-5 text-primary-500"></LoadingIcon>
                    <template #content>
                        This will clock the user out of their timesheet.
                    </template>
                </Tooltip>
            </SecondaryButton>
        </template>
        <template #alerts>
            <WarningAlert flush v-if="data.hidden_at !== null">
                <template #title>User Hidden</template>
                <p>
                    This user was marked as a leaver on the
                    {{ formatDateTime(data.hidden_at, 'do MMM yyyy') }} so will
                    not appear in any lists and cannot be assigned.
                </p>
            </WarningAlert>
        </template>
        <!-- <div class="p-5">
            <div class="grid grid-cols-4 gap-4 mb-3">
                <div class="col-span-1">
                    <img src="/images/badge-one-2.png" />
                </div>

                <div class="col-span-1">
                    <img src="/images/badge-two.png" />
                </div>

                <div class="col-span-1">
                    <img src="/images/badge-three.png" />
                </div>

                <div class="col-span-1">
                    <img src="/images/badge-four.png" />
                </div>
            </div>

        </div> -->
        <DetailsItem>
            <template #title>Contact Information</template>
            <Text>{{ data.mobile }}</Text>
            <Text class="truncate">{{ data.email }}</Text>
            <Text class="truncate">{{ data.home_venue?.title }}</Text>
            <!-- If the venue title doesnt include the postcode then show the postcode below -->
            <Text class="truncate">{{ !data.home_venue?.title.toLowerCase().includes(data.home_venue?.postcode.toLowerCase()) ? data.home_venue?.postcode : "" }}</Text>
        </DetailsItem>
        <DetailsItem>
            <template #title><span class="capitalize">{{ config?.terminology?.company ?? 'Company'}} Information</span></template>
            <Text>{{ data.company?.title }}</Text>
        </DetailsItem>
        <DetailsItem>
            <template #title>Passcode</template>
            <Text>{{ data.pass_code ?? '-' }}</Text>
        </DetailsItem>
        <DetailsItem v-if="data.employee_code && data.employee_code != ''">
            <template #title>Employee Code</template>
            <Text>{{ data.employee_code ?? '-' }}</Text>
        </DetailsItem>
        <DetailsItem
            v-if="
                data.cv_id &&
                (this.user().can('show-file') ||
                    (this.user().can('show-own-file') &&
                        data.owner_ids.includes(this.user().id)) ||
                    (this.user().can('show-managed-file') &&
                        this.user().managed_user_ids.some((r) =>
                            data.owner_ids.includes(r)
                        )))
            "
            class="items-center"
        >
            <template #title>CV</template>
            <File
                class="rounded border"
                :condensed="true"
                type="basic-list"
                :file-id="data.cv_id"
                :deletable="false"
                :downloadable="true"
            />
        </DetailsItem>
        <!--         <DetailsItem>
            <template #title>Latest Login</template>
        </DetailsItem> -->
        <DetailsItem>
            <template #title>Contact Preferences</template>
            <div class="flex items-center space-x-2">
                <StrongText>SMS:</StrongText>
                <BooleanText :data="data.contact_via_sms" />
            </div>
            <div class="flex items-center space-x-2">
                <StrongText>Email:</StrongText>
                <BooleanText :data="data.contact_via_sms" />
            </div>
        </DetailsItem>
        <DetailsItem>
            <template #title>Dates</template>
            <div class="flex items-center space-x-2">
                <StrongText>Start Date:</StrongText>
                <Text>{{
                    formatDateTime(data.start_date, 'do MMM yyyy')
                }}</Text>
            </div>
            <div
                v-if="data.end_date"
                class="flex items-center space-x-2"
            >
                <StrongText>End Date:</StrongText>
                <Text>{{ formatDateTime(data.end_date, 'do MMM yyyy') }}</Text>
            </div>
        </DetailsItem>
        <DetailsItem v-if="data.notes">
            <template #title>Notes</template>
            <div v-html="data.notes"></div>
        </DetailsItem>
    </Details>
</template>
<script>
import LoadingIcon from '../../Ui/LoadingIcon.vue';
import Tooltip from '../../Ui/Tooltip.vue';
import SecondaryButton from '../../Ui/Buttons/SecondaryButton.vue';
import DangerBadge from '../../Ui/Badges/DangerBadge.vue';
import SuccessBadge from '../../Ui/Badges/SuccessBadge.vue';
import Details from '../../Ui/Details/Details.vue';
import WarningAlert from '../../Ui/Alerts/WarningAlert.vue';
import Text from '../../Ui/Text/Text.vue';
import StrongText from '../../Ui/Text/StrongText.vue';
import BooleanText from '../../Ui/Text/BooleanText.vue';
import DetailsItem from '../../Ui/Details/DetailsItem.vue';
import File from '../Files/File.vue';
import axios from 'axios';
import formatDateTime from '../../../Filters/FormatDateTime';
import { getBackendClientConfig } from '../../../Support/client';
import { LogoutIcon } from '@heroicons/vue/outline';

export default {
    components: {
        DetailsItem,
        Details,
        Text,
        WarningAlert,
        File,
        StrongText,
        BooleanText,
        SuccessBadge,
        DangerBadge,
        SecondaryButton,
        Tooltip,

        LogoutIcon,
        LoadingIcon,
    },

    props: {
        data: {
            type: Object,
            required: true,
        },
    },

    data() {
        return {
            user: window.user,
            clockedIn: null,
            clockedInAt: null,
            clockedOutAt: null,
            loading: false,
            config: getBackendClientConfig(),
        }
    },

    mounted() {
        this.checkClockedIn();
    },

    methods: {
        formatDateTime,

        checkClockedIn() {
            axios.get('/api/v2/users/' + this.data.id + '/clocked-in')
                .then((response) => {
                    this.clockedIn = response.data.clockedIn;
                    this.clockedInAt = response.data.clockedInAt;
                    this.clockedOutAt = response.data.clockedOutAt;
                })
                .catch((error) => {
                    console.log(error);
                });
        },

        clockOut() {
            this.loading = true;
            axios.post('/api/v2/users/' + this.data.id + '/clock-out')
                .then((response) => {
                    this.checkClockedIn();
                    this.loading = false;
                })
                .catch((error) => {
                    console.log(error);
                });
        }
    },
};
</script>
