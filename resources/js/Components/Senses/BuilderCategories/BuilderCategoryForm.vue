<template>
    <AsideLayout v-bind="$props" :loading="state.is(AsideState.LOADING)">
        <template #title>{{ data.id ? 'Edit Builder Category' : 'Builder Category Form'}}</template>
        <SensesForm
            v-model="builderCategory"
            v-model:fields="fields"
            v-model:error="error"
            url="/api/v2/builder-categories"
            :id="data.id"
            @stateChange="state = $event"
            @success='formSuccess'
            :aside-index="asideIndex"
        ></SensesForm>
    </AsideLayout>
</template>

<script>
import AsideLayout from '../../Layout/AsideLayout.vue';
import SensesForm from '../Common/SensesForm.vue';
import Aside from '../../../Mixins/Aside';
import eventHub from '../../../Support/EventHub';
import AsideState from "../../../States/AsideState";

export default {
    components: {
        SensesForm,
        AsideLayout,
    },

    mixins: [Aside],

    data() {
        return {
            state: new AsideState(),
            AsideState,
            builderCategory: {
                title: null,
                slug: null,
            },
            error: null,
            fields: [
                {
                    title: 'Builder Category Information',
                    description: 'Basic information about the builder category',
                    fields: [
                        { key: 'title', type: 'text' },
                        { key: 'slug', type: 'text' },
                    ],
                },
            ],
        };
    },

    methods: {
        formSuccess(data) {
            eventHub.emit('builder-category-updated', data);
            this.$asides.pop();
        },
    },
};
</script>
