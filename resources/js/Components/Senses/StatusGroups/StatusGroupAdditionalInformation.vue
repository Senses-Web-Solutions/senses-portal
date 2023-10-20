<template>
    <Card flush>
        <template #title>Additional Information</template>
        <template #actions>
            <StatusGroupAdditionalInformationMenu :data="data" />
        </template>
        <CollapseGroup class="overflow-hidden rounded-b">
            <Collapse flush v-if="user().can('list-status')" ref="status-collapse">
                <template #title>Statuses</template>
                <template #badge>
                    <PrimaryBadge v-if="newStatus">New</PrimaryBadge>
                </template>
                <StatusTable :data="data" :condensed="true" as="div" :url="'/api/v2/status-groups/' + data.id + '/statuses'" />
            </Collapse>
        </CollapseGroup>
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
import StatusGroupAdditionalInformationMenu from './StatusGroupAdditionalInformationMenu.vue';
import StatusTable from '../Statuses/StatusTable.vue';
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
        StatusTable,
        Card,
        Badge,
        Collapse,
        CollapseGroup,
        StatusGroupAdditionalInformationMenu,
        SuccessBadge,
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
            newStatus: false,
            loading: false,
            additionalInformation: null,
        };
    },
    mounted() {
        // todo should this go in the maintainer?

        echo.private(`status-groups.${this.data.id}.statuses`).listen(
            'Statuses\\StatusCreated',
            () => this.updateCollapse('status', 'newStatus')
        );
        echo.private(`status-groups.${this.data.id}.statuses`).listen(
            'Statuses\\StatusUpdated',
            () => this.updateCollapse('status', 'newStatus')
        );
        echo.private(`status-groups.${this.data.id}.statuses`).listen(
            'Statuses\\StatusDeleted',
            () => this.updateCollapse('status', 'newStatus')
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
