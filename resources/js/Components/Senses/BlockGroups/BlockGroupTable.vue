<template>
    <SensesTable url="/api/v2/block-groups" :fields="fields" table="block_groups" @row-click="rowClick" :actions="actions" setting="table-block-groups" event="block-group-updated"></SensesTable>
</template>

<script>
import SensesTable from '../Tables/SensesTable.vue';

export default {

    components: {
        SensesTable
    },

    data() {
        return {
            user: window.user,
            actions: [],
            fields: [
                { label: "ID", key: "id" },
                { label: "Title", key: "display_name" },
                { label: "Description", key: "description" },
            ]
        }
    },


    methods: {
        rowClick(row, type) {
            if (row.id && this.user().can('show-block-group')) {
                window.location.href = "/block-groups/" + row.id;
            }
        },
    }

}
//Generated 29-09-2021 09:19:46
</script>

