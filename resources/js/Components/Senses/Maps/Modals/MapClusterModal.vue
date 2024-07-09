<template>
    <div class="h-full w-full relative bg-black bg-opacity-50 z-20 dark:bg-zinc-950 dark:bg-opacity-70" @click.self="close">
        <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 flex space-x-3 min-w-[500px] w-1/3 bg-white rounded-lg shadow p-6 inter">
            <div class="h-full w-full relative space-y-4">
                <div>
                    <div class="w-full flex justify-between">
                        <h2 class="text-xl">Select {{type}} in Cluster</h2>

                        <!-- Show selected count vs all in cluster -->
                        <div class="flex flex-col items-end tabular-nums">
                            <div class="flex items-center space-x-2 tabular-nums">
                                <p class="text-gray-500">Selected: {{selectedInClusterCount}}</p>
                                <p class="text-gray-500">/</p>
                                <p class="text-gray-500">Cluster: {{clusterCount}}</p>
                            </div>

                            <div class="flex items-center space-x-2">
                                <p class="text-gray-500">All {{type}}: {{selected.length}}</p>
                            </div>
                        </div>
                    </div>


                    <h3 class="text-gray-500 -mt-1">Refine your selection from the list below</h3>
                </div>

                <!-- Loop through models and use SeCheckbox, selected is true if model is in selectedModels -->
                <div class="max-h-[500px] overflow-y-auto space-y-2">
                    <div v-for="model in models" :key="model" class="w-full">
                        <label class="flex space-x-4 ml-1">
                            <SeCheckbox
                                :id="model.id_friendly"
                                :name="model.id_friendly"
                                :model-value="isSelected(model)"
                                @update:model-value="toggleSelectedModel(model)"
                            />
                            <div class="flex flex-col">
                                <p class="text-base">{{type}} {{model.id}} - {{model.title}}</p>
                                <SeLabel v-if="model?.venue?.title">{{model?.venue?.title}}</SeLabel>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="w-full flex space-x-4">
                    <SecondaryButton class="mr-auto" @click="toggleAll">Toggle All</SecondaryButton>
                    <SecondaryButton @click="close">Cancel</SecondaryButton>
                    <PrimaryButton @click="save">Save</PrimaryButton>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import SeCheckbox from '../../../Ui/Inputs/SeCheckbox.vue';
import SeLabel from '../../../Ui/Inputs/SeLabel.vue';

import SecondaryButton from '../../../Ui/Buttons/SecondaryButton.vue';
import PrimaryButton from '../../../Ui/Buttons/PrimaryButton.vue';

export default {
    components: {
        SeCheckbox,
        SeLabel,
        SecondaryButton,
        PrimaryButton,
    },
    props: {
        markers: {
            type: Array,
            required: true,
        },
        selectedModels: {
            type: Array,
            required: true,
        },
    },
    emits: ['close'],
    data() {
        return {
            selected: [...this.selectedModels],
        }
    },
    computed: {
        type() {
            return `${this.markers[0].type.charAt(0).toUpperCase() + this.markers[0].type.slice(1)}s`;
        },
        models() {
            return this.markers.map((marker) => marker.model);
        },
        clusterCount() {
            return this.models.length;
        },
        selectedInClusterCount() {
            return this.models.filter((model) => this.selected.includes(model)).length;
        },
        allSelectedInCluster() {
            return this.selectedInClusterCount === this.clusterCount;
        }
    },
    watch: {
        selectedModels(newVal) {
            this.selected = [...newVal];
        }
    },
    methods: {
        toggleSelectedModel(model) {
            if (this.selected.includes(model)) {
                this.selected = this.selected.filter((selectedModel) => selectedModel !== model);
            } else {
                this.selected.push(model);
            }
        },
        isSelected(model) {
            return this.selected.includes(model);
        },

        selectAll() {
            this.models.forEach((model) => {
                if (!this.selected.includes(model)) {
                    this.selected.push(model);
                }
            });
        },

        deselectAll() {
            this.models.forEach((model) => {
                if (this.selected.includes(model)) {
                    this.selected = this.selected.filter((selectedModel) => selectedModel !== model);
                }
            });
        },

        toggleAll() {
            if (!this.allSelectedInCluster) {
                this.selectAll();
            } else {
                this.deselectAll();
            }
        },

        close() {
            const data = this.prepDataForClose(this.selectedModels);

            this.$emit('close', data);
        },
        save() {
            const data = this.prepDataForClose(this.selected);

            this.$emit('close', data);
        },

        prepDataForClose(models) {
            const removeMarkers = this.markers.filter((marker) => !models.includes(marker.model));
            const addMarkers = this.markers.filter((marker) => models.includes(marker.model));

            return {
                models,
                markers: {
                    remove: removeMarkers,
                    add: addMarkers,
                }
            }
        }
    },
}
</script>