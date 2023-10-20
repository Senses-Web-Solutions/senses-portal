<template>
    <div>
        <FolderGridItem v-if="type === 'grid'" :folder="folder"
                      :id="index" :index="`file${index}`" :condensed="condensed">
            {{ index }}
        </FolderGridItem>
        <FolderDetailListItem v-else-if="type === 'detail-list'" :folder="folder"
                            :id="index"
                            :index="`file${index}`"
        >
            <div>{{ index }}</div>
        </FolderDetailListItem>
        <FolderBasicListItem v-else-if="type === 'basic-list'"
                           :folder="folder" :id="index" :index="`file${index}`">
            <div>{{ index }}</div>
        </FolderBasicListItem>
    </div>
</template>
<script>
import FolderGridItem from "../../Ui/Files/FolderGridItem.vue";
import FolderDetailListItem from "../../Ui/Files/FolderDetailListItem.vue";
import FolderBasicListItem from "../../Ui/Files/FolderBasicListItem.vue";
import formatDate from '../../../Filters/FormatDate';

export default {
    components: {FolderGridItem, FolderDetailListItem, FolderBasicListItem},
    props: {
        folder: {
            type: Array
        },
        condensed: {
            type: Boolean,
            default: false,
        },
        index: {
            default: 1
        },
        type: {
            type: String,
            default: "grid"
        }
    },
    data(){
      return {
      }
    },
    mounted() {
    },
    methods: {
        formatDate,
    }
}
</script>
