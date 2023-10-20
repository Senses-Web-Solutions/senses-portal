<template>
    <SensesTable
        :row-external-links="false"
        :url="url"
        :fields="fields"
        table="abilities"
        :event="['ability-group-updated', 'ability-updated']"
    />
</template>

<script>
import SensesTable from '../Tables/SensesTable.vue';

export default {

    components: {
        SensesTable
    },

    props: {
        url: {
            default: '/api/v2/abilities',
        }
    },

    data() {
        return {
            actions: [],
            fields: [
                { label: "ID", key: "id" },
                { label: "Title", key: "title" },
                // {label: "Slug", key: "name"},
            ]
        }
    },
}
</script>

//Generated 23-09-2021 09:47:48
