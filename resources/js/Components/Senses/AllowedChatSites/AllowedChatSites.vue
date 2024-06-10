<template>
    <!-- Use page layout -->

    <!-- Show block explaining how to add to site -->

    <!-- Show allowed sites table -->
    <AllowedChatSiteTable />
    <!-- Add button on table will create a new row -->

       
</template>
<script>
import AllowedChatSiteTable from '../AllowedChatSites/AllowedChatSiteTable.vue';

export default {
    components: {
        AllowedChatSiteTable,
    }
}
</script>