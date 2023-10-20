<template>
    <Card flush>
        <template #title>{{ title }} Details</template>

        <div class="divide-y divide-zinc-200 dark:divide-zinc-800">
            <InfoAlert class="rounded-t-lg" flush v-if="data?.locked_at">
                <template #icon>
                    <LockClosedIcon/>
                </template>
                <template #title>This <span class="lowercase">{{ title }}</span> is locked and can't be edited or deleted.</template>
            </InfoAlert>
            <template v-for="(field, key) in visibleData" :key="key">
                <div class="p-4 py-3 sm:grid sm:grid-cols-4 sm:gap-4 flex items-center">
                    <StrongText class="sm:col-span-2">
                        {{ makeTitle(key) }}
                    </StrongText>
                    <div class="mt-1 flex items-center sm:mt-0 sm:col-span-2 text-black">
                        <div v-if="key == 'created_at' || key == 'updated_at' || key == 'locked_at' || dateFields.includes(key)">
                            {{ formatDateTime(field) }}
                        </div>
                        <div v-else-if="typeof field == 'object' && field" @click="goToModel(key, field)" class="cursor-pointer truncate">
                            <Tag :colour="field.colour" v-if="Object.keys(field).includes('full_name')">
                                {{ field.full_name ?? field.first_name + ' ' + field.last_name ?? 'Invalid User' }}
                            </Tag>
                            <Tag :colour="field.colour" v-else-if="Object.keys(field).includes('title')">
                                {{ field.title }}
                            </Tag>
                            <Tag :colour="field.colour" v-else-if="Object.keys(field).includes('postcode')">
                                {{ field.postcode }}
                            </Tag>
                            <Tag :colour="field.colour" v-else-if="Object.keys(field).includes('id')">
                                {{ field.id }}
                            </Tag>
                            <span v-else>
                                <TagGroup>
                                    <Tag v-for="(item, key) in field" :key="key" :colour="item?.colour ?? 'gray'">
                                        <span v-if="Object.keys(item).includes('title')" class="capitalize">
                                            {{ item.title }}
                                        </span>
                                        <span v-else-if="Object.keys(item).includes('word')" class="capitalize">
                                            {{ item.word }}
                                        </span>
                                        <span v-else-if="Object.keys(item).includes('full_name')">
                                            {{ item.full_name }}
                                        </span>
                                        <span v-else-if="Object.keys(item).includes('postcode')">
                                            {{ item.postcode }}
                                        </span>
                                        <span v-else-if="Object.keys(field).includes('id')">
                                            {{ item.id }}
                                        </span>
                                        <span v-else>
                                            {{ item }}
                                        </span>
                                    </Tag>
                                </TagGroup>
                            </span>
                        </div>
                        <Colour v-else-if="key == 'colour'" :colour="field"></Colour>
                        <BooleanText v-else-if="typeof field == 'boolean'" :data="field"/>
                        <div v-else-if="currencyFields.includes(key)">{{ currency(field) }}</div>
                        <div v-else class="break-all">{{ field }}</div>
                    </div>
                </div>
            </template>
        </div>
    </Card>
</template>
<script>
import titleCase from '../../../Filters/TitleCase.js';
import Colour from '../../Ui/Colour.vue';
import Tag from '../../Ui/Tags/Tag.vue';
import TagGroup from '../../Ui/Tags/TagGroup.vue';
import BooleanText from '../../Ui/Text/BooleanText.vue' ;
import formatDateTime from '../../../Filters/FormatDateTime';
import Pluralize from 'pluralize';
import Card from '../../Ui/Cards/Card.vue';
import StrongText from "../../Ui/Text/StrongText.vue";
import InfoAlert from "../../Ui/Alerts/InfoAlert.vue";
import {LockClosedIcon} from '@heroicons/vue/outline';
import currency from '../../../Filters/Currency';

export default {
    components: {
        StrongText,
        Card,
        Colour,
        Tag,
        TagGroup,
        InfoAlert,
        BooleanText,
        LockClosedIcon
    },
    props: {
        title: {
            type: String,
            required: true,
        },
        model: {
            type: String,
            required: true,
        },
        id: {
            type: Number,
            required: false,
        },
        data: {
            type: Object,
            required: true,
        },
        dateFields: {
            type: Array,
            default: () => [],
        },
        currencyFields: {
            type: Array,
            default: () => [],
        },
        hiddenFields: {
            type: Array,
            default: () => [],
        },
        showUuid:{
            type: Boolean,
            default: true,
        }
    },

    data() {
        return {
            hide: ['id', 'uuid', 'app_id', 'text_colour', 'created_by', 'updated_by', 'deleted_by', 'deleted_at', 'owner_ids', 'hidden_at', 'hidden_by', 'object'],
        }
    },

    computed: {
        visibleData() {
            let visibleData = {};

            let nullChecks = ['locked_at', 'locked_by', 'lock_type', 'updated_at', 'updated_by', 'creator', 'updater'];

            if(!this.showUuid) {
                this.hide.push('uuid');
            }

            Object.keys(this.data).forEach((key) => {
                if (key.endsWith('_id') && Object.keys(this.data).includes(key.substring(0, key.length - 3))) {
                    // console.log('removed field');
                } else {
                    if (!(this.hiddenFields.concat(this.hide)).includes(key)) {
                        if (!nullChecks.includes(key) || this.data[key] !== null) {
                            visibleData[key] = this.data[key];
                        }
                    }
                }
            });
            return visibleData;
        },
    },

    methods: {
        currency,
        titleCase,
        formatDateTime,
        Pluralize,

        goToModel(key, model) {
            if (key === 'creator' || key === 'updater') {
                key = 'user'
            }
            window.location.href = '/' + Pluralize(key.replaceAll('_', '-')) + '/' + model.id;
        },

        makeTitle(key) {
            if(key === 'creator'){
                return 'Created By';
            } else if (key === 'updater'){
                return 'Updated By';
            }
            return titleCase(key);
        },
    },

    // TODO: Tags dont update with maintainer whilst other fields do update
};
</script>
