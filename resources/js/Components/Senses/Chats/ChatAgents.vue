<template>
    <TransitionGroup
        enter-active-class="transition duration-150 ease-out"
        enter-from-class="scale-75 opacity-0"
        enter-to-class="scale-100 opacity-100"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="scale-100 opacity-100"
        leave-to-class="scale-75 opacity-0"
    >
        <div v-for="agent in agents" :key="agent.id">
            <UserPopover
                panel-class="origin-top-left left-0"
                :id="agent?.id"
                :title="agent?.full_name"
            >
                <Tag
                    class="mr-2"
                    highlight-border
                    :highlight-dot="false"
                    colour="purple"
                >
                    {{ agent?.full_name }}
                </Tag>
            </UserPopover>
        </div>
    </TransitionGroup>
</template>
<script>
import UserPopover from '../Users/UserPopover.vue';
import Tag from '../../Ui/Tags/Tag.vue';

export default {
    components: {
        UserPopover,
        Tag,
    },
    props: {
        agents: {
            type: Array,
            required: true,
        },
    },
}
</script>