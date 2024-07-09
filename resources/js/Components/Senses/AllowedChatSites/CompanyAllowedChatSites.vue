<template>
    <!-- Use page layout -->

    <!-- Show block explaining how to add to site -->

    <!-- Show allowed sites table -->
    <CompanyAllowedChatSiteTable />
    <!-- Add button on table will create a new row -->

       
</template>
<script>
import CompanyAllowedChatSiteTable from '../AllowedChatSites/CompanyAllowedChatSiteTable.vue';

export default {
    components: {
        CompanyAllowedChatSiteTable,
    }
}
</script>