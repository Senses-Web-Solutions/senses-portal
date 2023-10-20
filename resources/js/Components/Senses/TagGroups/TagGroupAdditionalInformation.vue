<template>
    <Card flush>
        <template #title>Additional Information</template>
        <template #actions>
            <TagGroupAdditionalInformationMenu :data="data" />
        </template>
        <CollapseGroup class="overflow-hidden rounded-b">
            <Collapse flush v-if="user().can('list-tag')" ref="tag-collapse">
                <template #title>Tags</template>
                <template #badge>
                    <PrimaryBadge v-if="newTag">New</PrimaryBadge>
                </template>
                <TagTable :data="data" :condensed="true" as="div" :url="'/api/v2/tag-groups/' + data.id + '/tags'" />
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
import TagGroupAdditionalInformationMenu from './TagGroupAdditionalInformationMenu.vue';
import TagTable from '../Tags/TagTable.vue';
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
        TagTable,
        Card,
        Badge,
        Collapse,
        CollapseGroup,
        TagGroupAdditionalInformationMenu,
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
            newTag: false,
            loading: false,
            additionalInformation: null,
        };
    },
    mounted() {
        // todo should this go in the maintainer?

        echo.private(`tag-groups.${this.data.id}.tages`).listen(
            'Tags\\TagCreated',
            () => this.updateCollapse('tag', 'newTag')
        );
        echo.private(`tag-groups.${this.data.id}.tages`).listen(
            'Tags\\TagUpdated',
            () => this.updateCollapse('tag', 'newTag')
        );
        echo.private(`tag-groups.${this.data.id}.tages`).listen(
            'Tags\\TagDeleted',
            () => this.updateCollapse('tag', 'newTag')
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
