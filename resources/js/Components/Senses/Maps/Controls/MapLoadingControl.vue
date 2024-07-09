<template>
    <FadeTransition>
        <div v-if="show" class="pointer-events-auto relative flex rounded bg-white shadow max-h-[36px] max-w-[36px]">
            <Tooltip>
                <div class="rounded bg-white p-2 transition duration-150 ease-in-out">
                    <LoadingIcon v-if="loading" class="h-5 w-5 text-purple-700" />
                    <CheckIcon v-else-if="showCheckIcon && !showExclamationIcon" class="h-5 w-5 text-green-600" />
                    <ExclamationIcon v-if="!loading && invalidMarkersLength > 0 && showExclamationIcon" class="h-5 w-5 text-red-600" />
                </div>
                <template #content>{{ message }}</template>
            </Tooltip>
        </div>
    </FadeTransition>
</template>
<script>
import {CheckIcon, ExclamationIcon} from '@heroicons/vue/outline';

import FadeTransition from '../../../Ui/Transitions/FadeTransition.vue';
import LoadingIcon from '../../../Ui/LoadingIcon.vue';
import Tooltip from '../../../Ui/Tooltip.vue';

import EventHub from '../../../../Support/EventHub';

export default {
    components: {
        CheckIcon,
        ExclamationIcon,
        LoadingIcon,
        Tooltip,
        FadeTransition,
    },
    props: {
        loading: {
            type: Boolean,
            required: true,
        }
    },
    emits: ['updateSetting'],
    data() {
        return {
            threeDimensionalOn: this.threeDimensional,

            show: false,
            showCheckIcon: false,
            showExclamationIcon: false,

            invalidMarkers: {}
        };
    },
    computed: {
        message() {
            if (this.invalidMarkersLength > 0) {
                return `${this.invalidMarkersLength} Invalid Marker${this.invalidMarkersLength > 1 ? 's' : ''}`;
            }

            return this.loading ? 'Map Loading...' : 'Map Loaded';
        },
        invalidMarkersLength() {
            return Object.keys(this.invalidMarkers).length;
        },
    },
    watch: {
        loading(newVal) {
            if (!newVal) {
                this.show = true;
                this.showCheckIcon = true;
                this.showExclamationIcon = false;

                setTimeout(() => {
                    this.showCheckIcon = false;

                    if (this.invalidMarkersLength === 0) {
                        this.show = false;
                    } else {
                        this.showExclamationIcon = true;
                    }
                }, 3000);
            } else {
                this.show = true;
            }
        }
    },
    mounted() {
        if (this.loading) {
            this.show = true;
        }

        EventHub.on('map:invalid-markers', (invalidMarkers) => {
            this.invalidMarkers = invalidMarkers;

            if (this.invalidMarkersLength > 0) {
                this.show = true;
                this.showExclamationIcon = true;
            }
        });
    },
    beforeUnmount() {
        EventHub.off('map:invalid-markers');
    },
    methods: {
        threeDimensionalMap() {
            this.threeDimensionalOn = true;
            this.emitUpdate();
        },

        twoDimensionalMap() {
            this.threeDimensionalOn = false;
            this.emitUpdate();
        },

        emitUpdate() {
            this.$emit('updateSetting', { key: 'threeDimensional', value: this.threeDimensionalOn });
            EventHub.emit('map:three_dimensional', this.threeDimensionalOn);
        },
    },
};
</script>
