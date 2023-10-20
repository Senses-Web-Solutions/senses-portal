<template>
    <SensesTable url="/api/v2/builder-categories" :fields="fields" table="builder_categories" @row-click="rowClick"
                 @action-applied="actionApplied" :actions="actions" setting="table-builder-categories"
                 event="builder-category-updated"></SensesTable>
</template>

<script>
import SensesTable from '../Tables/SensesTable.vue';

export default {

    components: {
        SensesTable
    },

    data() {
        return {
            user: window.user(),
            actions: [],
            fields: [
                {label: "ID", key: "id"},
                {label: "Title", key: "title"},
                {label: "Slug", key: "slug"},
            ]
        }
    },


    methods: {
        rowClick(row, type) {
            if (row.id && this.user().can('show-builder-category')) {
                if (type && type === 'blank') {
                    window.open("/builder-categories/" + row.id);
                } else {
                    window.location.href = "/builder-categories/" + row.id;
                }
            }
        },
        actionApplied(type, rows) {
            console.log(type, rows);
        }
    }

}
//Generated 29-09-2021 09:19:46
</script>

