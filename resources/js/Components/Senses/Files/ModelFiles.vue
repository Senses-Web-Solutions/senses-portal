<template>
    <Card flush>
        <template #title>Files</template>
    <div class="border-b flex justify-between items-center">
        <div class="flex p-5 text-zinc-600">
            <div
                v-for="(chunk, index) in path"
                :key="`filesChunkPath${chunk}${index}`"
                @click="up(index)"
            >
                {{ chunk }} <span
                class="text-zinc-400 px-3"
                v-if="index !== path.length - 1"
            >/</span>
            </div>
        </div>
        <div class="py-1 pr-5 flex items-center">
            <ButtonGroup>
                <SecondaryButton
                    size="xs"
                    @click="alert('download not implemented')"
                >
                    <FolderDownloadIcon class="w-4 h-4"/>
                </SecondaryButton>
                <div>
          <span class="relative z-0 inline-flex shadow-sm rounded-md">
            <SecondaryButton
                :class="view === 'grid' ? `!bg-zinc-200` : ''"
                @click="view = 'grid'"
                size="xs"
                rounded="left"
            >
              <ViewGridIcon class="w-4 h-4"/>
            </SecondaryButton>
            <SecondaryButton
                :class="view === 'detail-list' ? `!bg-zinc-200` : ''"
                @click="view = 'detail-list'"
                size="xs"
                rounded="none"
            >
              <ViewBoardsIcon class="w-4 h-4 rotate-90"/>
            </SecondaryButton>
            <SecondaryButton
                :class="view === 'basic-list' ? `!bg-zinc-200` : ''"
                @click="view = 'basic-list'"
                size="xs"
                rounded="right"
            >
              <ViewListIcon class="w-4 h-4"/>
            </SecondaryButton>
          </span>
                </div>
                <SecondaryButton
                    size="xs"
                    @click="up"
                >
                    <ArrowUpIcon class="w-4 h-4"/>
                </SecondaryButton>
            </ButtonGroup>
        </div>
    </div>
    <div
        class="p-5"
        :class="{
                'flex flex-wrap gap-5': view === 'grid',
            }"
        v-bind="$attrs"
    >
        <template v-for="(file, key) in visibleFiles">
            <FileGridItem
                v-if="view === 'grid'"
                :folder="Array.isArray(file)"
                :file="file"
                @click="Array.isArray(file) ? enter(key) : null"
                :id="key"
                :key="`file${key}`"
            >
                {{ Array.isArray(file) ? key : formatDate(file.created_at) }}
            </FileGridItem>
            <FileDetailListItem
                v-else-if="view === 'detail-list'"
                :folder="Array.isArray(file)"
                :file="file"
                @click="Array.isArray(file) ? enter(key) : null"
                :id="key"
                :key="`file${key}`"
            >
                <div>
                    {{ Array.isArray(file) ? key : formatDate(file.created_at) }}
                </div>
                <div
                    class="text-sm text-zinc-500"
                    v-if="!Array.isArray(file)"
                >Uploaded by USER on SHIFT
                </div>

                <template #end>
                    <PrimaryBadge v-if="!Array.isArray(file)">{{ file.extension }}</PrimaryBadge>
                </template>
            </FileDetailListItem>
            <FileBasicListItem
                v-else-if="view === 'basic-list'"
                :folder="Array.isArray(file)"
                :file="file"
                @click="Array.isArray(file) ? enter(key) : null"
                :id="key"
                :key="`file${key}`"
            >
                <div>
                    {{ Array.isArray(file) ? key : formatDate(file.created_at) }}
                </div>

                <template #end>
                    <PrimaryBadge v-if="!Array.isArray(file)">{{ file.extension }}</PrimaryBadge>
                </template>
            </FileBasicListItem>
        </template>
    </div>
    <svg class="w-0 h-0">
        <clipPath
            id="documentClipPath"
            clipPathUnits="objectBoundingBox"
        >
            <path
                d="M0.143,1 H0.857 A0.143,0.111,0,0,0,1,0.889 V0.356 A0.071,0.056,0,0,0,0.979,0.317 L0.592,0.016 A0.071,0.056,0,0,0,0.542,0 H0.143 A0.143,0.111,0,0,0,0,0.127 V0.905 A0.143,0.111,0,0,0,0.143,1"></path>
        </clipPath>
    </svg>
    </Card>
</template>
<script>
import {
    ArrowUpIcon,
    ViewGridIcon,
    ViewListIcon,
    ViewBoardsIcon,
} from "@heroicons/vue/solid";
import {FolderDownloadIcon} from "@heroicons/vue/outline";
import {computed, ref} from "vue";
import SecondaryButton from "../../Ui/Buttons/SecondaryButton.vue";
import ButtonGroup from "../../Ui/Buttons/ButtonGroup.vue";
import Card from "../../Ui/Cards/Card.vue";
import FileGridItem from "../../Ui/Files/FileGridItem.vue";
import FileDetailListItem from "../../Ui/Files/FileDetailListItem.vue";
import FileBasicListItem from "../../Ui/Files/FileBasicListItem.vue";
import PrimaryBadge from "../../Ui/Badges/PrimaryBadge.vue";
import {get} from "lodash-es";
import formatDate from '../../../Filters/FormatDate';
import axios from "axios";
import FileForm from './FileForm.vue';
import Pluralize from 'pluralize';

//todo make this generic, company version should only pass the data in? Its also got templates that rely on slots for info but uses url in the templates?  (component is and view stuff within each type!)
//todo add pusher/maintainer bits, backend should be broadcasting an event similiar to comments
export default {
    components: {
        Card,
        ArrowUpIcon,
        ViewGridIcon,
        ViewListIcon,
        ViewBoardsIcon,
        FolderDownloadIcon,
        SecondaryButton,
        ButtonGroup,
        FileGridItem,
        FileDetailListItem,
        FileBasicListItem,
        PrimaryBadge,
        FileForm
    },
    props: {
        id: {
            required: true
        },
        model: {
            type: String,
            required: true
        }
    },
    setup(props) {
        const path = ref(["Home"]);
        const files = ref(null);

        axios.get('/api/v2/' + Pluralize(props.model) + '/' + props.id + '/files?format=folders').then(response => files.value = response.data);

        const view = ref("grid");

        const visibleFiles = computed(() => {
            const actualPath = [...path.value];
            actualPath.shift(); // remove company XXXXXX
            if (actualPath.length === 0) {
                return files.value;
            }
            return get(files.value, actualPath.join("."));
        });

        const up = (index = null) => {
            if (index && index == path.value.length - 1) {
                return; //ignore removing if clicked from current level
            }
            if (path.value.length > 1) {
                path.value.pop(); //todo currently breadcrumbs only go up one, where multilevel should take you to correct level
            }
        };

        const enter = (folder) => path.value.push(folder);

        const formFileables = [{id: props.id, type: props.model}];

        return {up, enter, visibleFiles, view, path, files, formatDate, formFileables}
    }
}

</script>
