<template>
    <nav class="flex-1 px-2 py-4 flex flex-col justify-between" data-sidebar>
        <template v-if="user && user()">
            <div class="space-y-1">
                <!-- Featured -->
                <SidebarGroup v-if="Route.startsWith(`/tasks/${Route.id()}`) && (user().can('show-task') || user().can('show-own-task'))" :title="`Task ${Route.id()}`">
                    <template #icon>
                        <ArrowCircleRightIcon/>
                    </template>
                    <SidebarSubItem title="Detail" :to="`/tasks/${Route.id()}`" :active="Route.is('tasks', 'show')"/>
                    <SidebarSubItem title="Highlights" v-if="user().can('show-task')" :to="`/tasks/${Route.id()}/highlights`" :active="Route.is('tasks', 'index', Route.id(), 'highlights')"/>
                    <SidebarSubItem title="Shifts" v-if="user().can('list-assignment-group')" :to="`/tasks/${Route.id()}/shifts`" :active="Route.is('tasks', 'index', Route.id(), 'shifts')"/>
                    <SidebarSubItem title="Earnings" v-if="user().can('list-revenues')" :to="`/tasks/${Route.id()}/earnings`" :active="Route.is('tasks', 'index', Route.id(), 'earnings')"/>
                    <SidebarSubItem title="On Site" v-if="user().can('list-assignment-group')" :to="`/tasks/${Route.id()}/on-site`" :active="Route.is('tasks', 'index', Route.id(), 'on-site')"/>
                    <SidebarSubItem title="Inbox" v-if="user().can('show-task')" :to="`/tasks/${Route.id()}/inbox`" :active="Route.is('tasks', 'index', Route.id(), 'inbox')"/>
                    <SidebarSubItem title="Client Applications" v-if="user().can('list-client-application')" :to="`/tasks/${Route.id()}/client-applications`" :active="Route.is('tasks', 'index', Route.id(), 'client-applications')"/>
                </SidebarGroup>

                <SidebarItem title="Dashboard" to="/" :active="Route.matches('/')">
                    <template #icon>
                        <HomeIcon />
                    </template>
                </SidebarItem>

                <SidebarItem :class="userCanSee(dashboard.report_layout.roles)" v-for="dashboard in dashboards" :title="dashboard.report_layout.title" :to="'/report-layouts/' + dashboard.report_layout.slug" :active="Route.is('report-layouts', dashboard.report_layout.slug)">
                    <template #icon>
                        <InformationCircleIcon />
                    </template>
                </SidebarItem>

                <SidebarItem title="Tasks" v-if="user().can('list-task') || user().can('list-client-task')" to="/tasks" :active="Route.is('tasks')">
                    <template #icon>
                        <CollectionIcon />
                    </template>
                </SidebarItem>
            </div>

            <div>
                <SidebarItem title="Live Chat" to="#" @click.prevent.stop="openLivechat">
                    <template #icon>
                        <ChatIcon />
                    </template>
                </SidebarItem>
            </div>
        </template>
    </nav>
</template>
<script>
import {
    HomeIcon,
    CollectionIcon,
    ChatIcon,
    InformationCircleIcon,
    ArrowCircleRightIcon,
} from "@heroicons/vue/outline";
import axios from 'axios';
import { mapStores } from "pinia";
import SidebarItem from "./SidebarItem.vue";
import SidebarGroup from "./SidebarGroup.vue";
import SidebarSubItem from "./SidebarSubItem.vue";
import Route from '../../../Support/Route';
import awaitElement from "../../../Support/awaitElement";
import useSidebarStore from "../../../Stores/useSidebarStore";
import user from "../../../Support/user";

export default {
    components: {
        SidebarItem, SidebarGroup, SidebarSubItem,
        HomeIcon,
        CollectionIcon,
        ChatIcon,
        InformationCircleIcon,
        ArrowCircleRightIcon,
    },
    provide() {
        return {
            sidebar: this.sidebar
        }
    },
    data() {
        return {
            Route,
            user,
            pageTitle: '',
            sidebar: [],
            error: null,
            dashboards: []
        }
    },
    computed: {
        ...mapStores(useSidebarStore)
    },
    watch: {
        sidebar() {
            this.sidebarStore.sidebar = [...this.sidebar];
        }
    },
    mounted() {
        awaitElement('#page-header-title', 25, 10000).then((element) => {
            this.pageTitle = element.innerText.replace(`${Route.id()}: `, '');
        });
        this.sidebarStore.sidebar = [...this.sidebar];
        this.loadSidebarDashboards();
    },
    methods: {
        userCanSee(roles){
            var canSee = 'hidden';
            if(roles.length){
                roles.forEach(role => {
                    if(this.user().hasRole(role)){
                        canSee = 'block';
                    }
                });
            } else {
                canSee = 'block';
            }
            return canSee;
        },
        openLivechat() {
            document.querySelector('.lz_overlay_wm_button').click();
        },
        loadSidebarDashboards(){
            axios
                .get('/api/v2/users/' + this.user().id + '/sidebar-dashboards')
                .then((response) => {
                    this.dashboards = response.data;
                })
                .catch((error) => {
                    this.error = error;
                });
        },
    }
}
</script>
