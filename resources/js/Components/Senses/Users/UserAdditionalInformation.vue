<template>
    <Card flush>
        <template #title>Additional Information</template>
        <template #actions>
            <UserAdditionalInformationMenu :data="data" />
        </template>
    </Card>
</template>
<script>
import axios from 'axios';
import DangerBadge from '../../Ui/Badges/DangerBadge.vue';
import SuccessBadge from '../../Ui/Badges/SuccessBadge.vue';
import Collapse from '../../Ui/Collapse/Collapse.vue';
import CollapseGroup from '../../Ui/Collapse/CollapseGroup.vue';
import Card from '../../Ui/Cards/Card.vue';
import Badge from '../../Ui/Badges/Badge.vue';
import PrimaryBadge from '../../Ui/Badges/PrimaryBadge.vue';
import UserAdditionalInformationMenu from './UserAdditionalInformationMenu.vue';
// import ServiceTable from '../Services/ServiceTable.vue';
// import OrganisationTable from '../Organisations/OrganisationTable.vue';
import QuickAddButton from "../../Ui/Buttons/QuickAddButton.vue";
import TransitionTimings from '../../../Enums/TransitionTimings';
import currency from '../../../Filters/Currency';
import highlight from '../../../Support/highlight';

import useEcho from '../../../Support/useEcho';

const echo = useEcho();

export default {
    components: {
        PrimaryBadge,
        QuickAddButton,
        // ServiceTable,
        Card,
        Badge,
        Collapse,
        CollapseGroup,
        UserAdditionalInformationMenu,
        SuccessBadge,
        // OrganisationTable,
        DangerBadge,
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
            TransitionTimings,
            newService: false,
            newOrganisation: false,
            loading: false,
            additionalInformation: null,
        };
    },
    mounted() {
        // todo should this go in the maintainer?

        echo.private(`users.${this.data.id}.services`).listen(
            'Services\\ServiceCreated',
            () => this.updateCollapse('service', 'newService')
        );
        echo.private(`users.${this.data.id}.services`).listen(
            'Services\\ServiceUpdated',
            () => this.updateCollapse('service', 'newService')
        );
        echo.private(`users.${this.data.id}.services`).listen(
            'Services\\ServiceDeleted',
            () => this.updateCollapse('service', 'newService')
        );


        echo.private(`users.${this.data.id}.organisations`).listen(
            'Organisations\\OrganisationCreated',
            () => this.updateCollapse('organisation', 'newOrganisation')
        );
        echo.private(`users.${this.data.id}.organisations`).listen(
            'Organisations\\OrganisationUpdated',
            () => this.updateCollapse('organisation', 'newOrganisation')
        );
        echo.private(`users.${this.data.id}.organisations`).listen(
            'Organisations\\OrganisationDeleted',
            () => this.updateCollapse('organisation', 'newOrganisation')
        );

    },
    methods: {
        currency,
        updateCollapse(item, field) {
            this.$nextTick(() => {
                highlight(this.$refs[item + '-collapse'].$el);
                this[field] = true;
            });
        },
        formatBadge(count, trueComponent, falseComponent) {
            return count > 0 ? trueComponent : falseComponent;
        },
    },
};
</script>
