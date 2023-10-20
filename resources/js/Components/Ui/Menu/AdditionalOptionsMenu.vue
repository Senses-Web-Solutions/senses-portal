<template>
    <SeMenu :data="data">
        <template #items>
            <slot></slot>
            <MenuItem
                v-if="refreshable"
                v-slot="{ active }"
                :key="`modelButtons${0}`"
                :style="{
                    '--tw-transition-delay': `${TransitionTimings.STAGGERED_LIST_DELAY}ms`,
                }">
                <span>
                    <button :class="[
                                active || !enabled ? 'bg-zinc-50 ' : '',
                                !enabled ? 'text-zinc-500' : '',
                                'group flex w-full items-center rounded-md py-2 px-3 text-zinc-700 cy_refresh',
                            ]" @click="enabled ? refreshModel() : null">
                        <RefreshIcon class="w-5 h-5 mr-2"/>
                        Refresh
                    </button>
                </span>
            </MenuItem>
            <MenuItem
                :disabled="!enabled"
                v-slot="{ active }"
                :key="`modelButtons${1}`"
                :style="{
                    '--tw-transition-delay': `${TransitionTimings.STAGGERED_LIST_DELAY}ms`,
                }">
            <component :is="!enabled ? 'Tooltip' : 'span'">
                <button :class="[
                            active || !enabled ? 'bg-zinc-50 ' : '',
                            !enabled ? 'text-zinc-500' : '',
                            'group flex w-full items-center rounded-md py-2 px-3 text-zinc-700 cy_delete',
                        ]" @click="enabled ? deleteModel() : null">
                    <TrashIcon class="w-5 h-5 mr-2"></TrashIcon>
                    Delete
                </button>
                <template v-if="!enabled" #content>
                    <div v-if="disableType === 'locked'">
                        This {{ model.replaceAll('-', ' ') }} is locked
                    </div>
                    <div v-else-if="disableType === 'denied'">
                        Permission denied
                    </div>
                </template>
            </component>
            </MenuItem>
        </template>
    </SeMenu>
</template>

<script>
import {
    MenuItem
} from '@headlessui/vue';
import Tooltip from '../Tooltip.vue';
import TransitionTimings from '../../../Enums/TransitionTimings';
import SeMenu from '../../Ui/Menu/SeMenu.vue';
import axios from 'axios';
import Pluralize from 'pluralize';
import EventHub from '../../../Support/EventHub';
import {
    TrashIcon,
    RefreshIcon
} from "@heroicons/vue/outline";

export default {
    components: {
        MenuItem,
        SeMenu,
        Tooltip,
        TrashIcon,
        RefreshIcon,
    },
    props: {
        data: {
            type: Object,
            required: true,
        },
        model: {
            type: String,
            required: true,
        },
        id: {
            type: Number,
            required: true,
        },
        refreshable:{
            type:Boolean,
            default:false
        }
    },
    data() {
        return {
            user: window.user,
            TransitionTimings,
            disableType: null,
        };
    },
    computed: {
        enabled() {
            if (this.data?.locked_at !== null) {
                this.disableType = 'locked';
                return false;
            }

            if (
                this.model !== 'user' &&
                !this.user().can(`delete-${this.model}`) &&
                !(
                    this.data.owner_ids &&
                    this.user().can(`delete-own-${this.model}`) &&
                    this.data.owner_ids.includes(this.user().id)
                ) &&
                !(
                    this.user().managed_user_ids &&
                    this.user().can(`delete-managed-${this.model}`) &&
                    this.user().managed_user_ids.some((r) =>
                        this.data.owner_ids.includes(r)
                    )
                )
            ) {
                this.disableType = 'denied';
                return false;
            }

            if (
                this.model === 'user' &&
                !this.user().can(`delete-user`) &&
                !(
                    this.user().can(`delete-own-user`) &&
                    this.data.id === this.user().id
                ) &&
                !(
                    this.user().managed_user_ids &&
                    this.user().can(`delete-managed-user`) &&
                    this.user().managed_user_ids.includes(this.data.id)
                )
            ) {
                this.disableType = 'denied';
                return false;
            }

            this.disableType = null;
            return true;
        },
        pluralModel() {
            return Pluralize(this.model);
        },
    },
    methods: {
        async deleteModel() {
            const confirmed = await this.$dialogs.danger(
                'Are you sure?',
                `You won't be able to access this ${this.model.replaceAll(
                    '-',
                    ' '
                )} again.`
            );

            if (confirmed) {
                axios
                    .delete(`/api/v2/${this.pluralModel}/${this.id}`)
                    .then((response) => {
                        EventHub.emit(`${this.model}-updated`);
                        EventHub.emit(`${this.model}-deleted`, this.id);

                        window.location = `/${this.pluralModel}`;
                    })
                    .catch((error) => {
                        console.log(error);
                        this.$emit('error', error.response.data);
                        this.$notifications.push({
                            title: 'Delete failed',
                            description: error.response.data.message,
                            type: 'danger',
                        });
                    });
            }
        },

        refreshModel(){
            axios
            .post(`/api/v2/${this.pluralModel}/${this.id}/flush-cache`)
            .then((response) => {
                window.location.reload();
            })
            .catch((error) => {
                this.$notifications.push({
                    title: 'Refresh failed',
                    description: error.response.data.message,
                    type: 'danger',
                });
            })
        }
    },
};
</script>
