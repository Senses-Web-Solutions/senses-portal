<template>
        <template v-if="model">
            <div class="flex justify-between">
                <div class="leading-snug">
                    {{ model[field] }} <br />
                </div>
            </div>
        </template>
        <div v-else class="flex items-center justify-center">
            <LoadingIcon class="my-2 h-4 w-4" />
        </div>
</template>
<script>
import { ref } from 'vue';
import LoadingIcon from './LoadingIcon.vue';
import CachedRequest from '../../Support/CachedRequest';

export default {
    components: { LoadingIcon },
    props: {
        id: {
            type: [Number, String],
            required: true,
        },
        field: {
            type: String,
            required: true,
        },
        modelName: {
            type: String,
            required: true,
        }
    },
    setup(props) {
        const model = ref(null);
        CachedRequest.get(`/api/v2/${props.modelName}s/${props.id}`).then((response) => {
            model.value = response.data;
        });
        return { model };
    },
};
</script>
