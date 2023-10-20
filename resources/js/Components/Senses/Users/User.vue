<template>
    <div class="flex">
        <GenericIcon :model="user" v-if="icon" class="mr-2"/>
        <dt class="my-auto" v-if="user">
            <UserPopover panel-class="origin-top-left left-0" :id="user?.id" :title="user?.full_name">{{ user?.full_name }}</UserPopover>
        </dt>
    </div>
</template>
<script>
import UserPopover from "../Users/UserPopover.vue";
import GenericIcon from "../../Ui/GenericIcon.vue";
//todo not entirely sure we need this component wrapper, its used in comment.vue
export default {
    components: { UserPopover, GenericIcon},
    props: {
        user: {
            type: Object,
            required: true,
        },
        icon: {
            default: true
        }
    },
    data() {
        return {};
    },
    mounted() {
    },
    methods: {},
};
</script>
