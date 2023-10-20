<template>
    <Card flush>
        <template #title>Abilities</template>
        <AbilityTable :url="'/api/v2/ability-groups/' + data.id + '/abilities'" condensed as="div"/>
    </Card>
</template>

<script>
import AbilityTable from "../Abilities/AbilityTable.vue";
import Card from "../../Ui/Cards/Card.vue";

export default {
    components: {
        Card,
        AbilityTable
    },

    props: {
        data: {
            type: Object,
            required: true
        }
    }
}
</script>
