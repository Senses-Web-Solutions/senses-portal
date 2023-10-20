<template>
    <SensesTable
        url="/api/v2/ability-groups"
        table="ability_groups"
        setting="table-ability-groups"
        event="ability-group-updated"
        :fields="fields"
        :actions="actions"
        @row-click="rowClick"
    />
</template>

<script>
import SensesTable from '../Tables/SensesTable.vue';

export default {

    components: {
        SensesTable
    },

    props:{
        url:{
            default:'/api/v2/ability-groups',
        }
    },

    data() {
        return {
            actions: [],
            fields: [
                {label: "ID", key: "id"},
                {label: "Title", key: "title"},
            ]
        }
    },
    methods:{
        rowClick(row, type) {
            if (row.id) {
                if (type && type === 'blank') {
                    window.open('/ability-groups/' + row.id);
                } else {
                    window.location.href = '/ability-groups/' + row.id;
                }
            }
        },
    }
}
</script>

//Generated 23-09-2021 09:47:48
