<template>
    <AsideLayout v-bind="$props">
        <template #title>Update User Admin</template>
        <SensesForm
            v-model="user"
            v-model:error="error"
            v-model:fields="fields"
            :url="url"
            @success="formSuccess"
        />
    </AsideLayout>
</template>

<script>
import AsideLayout from '../../Layout/AsideLayout.vue';
import SensesForm from '../Common/SensesForm.vue';
import Aside from '../../../Mixins/Aside';
import AsideState from '../../../States/AsideState';

export default {
    components: {
        AsideLayout,
        SensesForm,
    },

    mixins: [Aside],
    props: {
        data: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            error: null,
            AsideState,
            state: new AsideState(),
            fields: [
                {
                    title: 'HR',
                    description: 'Basic HR information about the user.',
                    fields: [
                        {
                            key: 'holiday_allowance',
                            type: 'number',
                        },
                    ],
                },
            ],
            user: {
                holiday_allowance: this.data.holiday_allowance,
            },
        };
    },
    computed: {
        url() {
            return '/api/v2/users/' + this.data.id + '/admin';
        },
    },
    methods: {
        formSuccess(response) {
            this.state.set(AsideState.SUCCESS);
            this.$asides.pop();
        },
    },
};
</script>
