<template>
    <SensesTable
        :url="url"
        :row-external-links="false"
        table="statuses"
        setting="table-statuses"
        event="status-updated"
        :fields="fields"
        :actions="actions"
        @row-click="rowClick">
        <template #colour="slotProps">
            <td :class="slotProps.class">
                <Colour :colour="slotProps.row.colour"></Colour>
            </td>
        </template>
    </SensesTable>
</template>

<script>
import Colour from '../../Ui/Colour.vue';
import SensesTable from '../Tables/SensesTable.vue';

export default {
    components: {
        SensesTable,
        Colour,
    },

    props: {
        url: {
            type: String,
            default: '/api/v2/statuses'
        }
    },

    data() {
        return {
            user: window.user,
            actions: [],

            fields: [
                { label: "ID", key: "id" },

                { label: "Title", key: "title" },
                { label: "Slug", key: "slug" },
                { label: "Colour", key: "colour" },
            ]
        }
    },

    methods: {
        rowClick(row, type) {
            if (row.id && user().can('show-status')) {
                this.$asides.push('StatusForm', {
                    id: row.id,
                });
            }
        }
    }
}
</script>

// Generated 09-10-2023 12:35:29
