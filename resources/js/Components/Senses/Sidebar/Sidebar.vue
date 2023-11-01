<template>
    <nav class="flex-1 px-2 py-4 flex flex-col justify-between" data-sidebar>
        <template v-if="user && user()">
            <div class="space-y-1">
                <SidebarItem title="Dashboard" to="/" :active="Route.matches('/')">
                    <template #icon>
                        <HomeIcon />
                    </template>
                </SidebarItem>

				<SidebarItem title="Users" v-if="user().can('list-user')" to="/users" :active="Route.is('users', 'any')" />
				<SidebarItem title="Servers" v-if="user().can('list-server')" to="/servers" :active="Route.is('servers', 'any')" />
				<SidebarItem title="Companies" v-if="user().can('list-company')" to="/companies" :active="Route.is('companies', 'any')" />
				<!-- ---------- GENERATOR ---------- -->

                <SidebarGroup title="Intel">
                    <template #icon>
                        <CogIcon />
                    </template>

                    <SidebarSubItem title="Statuses" v-if="user().can('list-status')" to="/statuses" :active="Route.is('statuses', 'any')" />
                    <SidebarSubItem title="Status Groups" v-if="user().can('list-status-group')" to="/status-groups" :active="Route.is('status-groups', 'any')" />
                    <SidebarSubItem title="Tags" v-if="user().can('list-tag')" to="/tags" :active="Route.is('tags', 'any')" />
                    <SidebarSubItem title="Tag Groups" v-if="user().can('list-tag-group')" to="/tag-groups" :active="Route.is('tag-groups', 'any')" />
                    <SidebarSubItem title="Ability Groups" v-if="user().can('list-ability-group')" to="/ability-groups" :active="Route.is('ability-groups', 'any')" />
                </SidebarGroup>

                <!-- <SidebarGroup title="Communications">
                    <template #icon>
                        <AnnotationIcon />
                    </template>

                    <SidebarSubItem title="Email Logs" v-if="user().can('list-email-log')" to="/email-logs" :active="Route.is('email-logs', 'any')" />
                    <SidebarSubItem title="SMS Logs" v-if="user().can('list-sms-log')" to="/sms-logs" :active="Route.is('sms-logs', 'any')" />
                </SidebarGroup> -->

                <SidebarGroup title="Senses" v-if="user()?.email?.endsWith('@senses.co.uk')">
                    <template #icon>
                        <EmojiHappyIcon />
                    </template>
                    <SidebarSubItem title="Reseed Abilities" to="/abilities/reseed" :active="Route.matches('/abilities/reseed')" v-if="user()?.email?.endsWith('@senses.co.uk')">
                        <template #icon>
                            <LockClosedIcon />
                        </template>
                    </SidebarSubItem>
                </SidebarGroup>
            </div>

            <!-- <div class="border-t border-zinc-200  inset-x-0 bottom-8 px-2 mt-4">
                <p class="text-zinc-500 text-sm uppercase font-medium px-2 mt-2 pt-2 pb-4">Help and Support</p>
                <a href="#" @click.prevent.stop="openLivechat" class="text-zinc-700 hover:bg-primary-200 group flex items-center px-2 py-1.5 text-sm rounded-md">
                    <div class="rounded-full bg-purple-600 mr-2 p-1.5 ">
                        <ChatIcon  class="w-4 h-4 shrink-0 text-white"/>
                    </div>
                    Live Chat
                </a>
            </div> -->
        </template>
    </nav>
</template>

<script>
import {
    DatabaseIcon,
    HomeIcon,
    CollectionIcon,
    CalendarIcon,
    LockClosedIcon,
    ChatIcon,
    AnnotationIcon,
    BriefcaseIcon,
    ClipboardListIcon,
    ShoppingCartIcon,
    ClockIcon,
    CurrencyPoundIcon,
    ChartBarIcon,
    UsersIcon,
    TruckIcon,
    CogIcon,
    EmojiHappyIcon,
    DocumentReportIcon,
    ArrowCircleRightIcon,
    TemplateIcon,
    InformationCircleIcon,
    TerminalIcon,
    MapIcon,
    UserCircleIcon,
    AcademicCapIcon,
    GlobeAltIcon,
    CheckIcon,
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
import { getBackendClientConfig, isClient } from '../../../Support/client';
import pluralize from 'pluralize';
import { capitalize } from 'lodash-es';
import Tooltip from '../../Ui/Tooltip.vue';


export default {
    components: {
        SidebarItem, SidebarGroup, SidebarSubItem,
        DatabaseIcon,
        HomeIcon,
        CollectionIcon,
        Tooltip,
        AnnotationIcon,
        ChatIcon,
        CalendarIcon,
        BriefcaseIcon,
        EmojiHappyIcon,
        ClipboardListIcon,
        ShoppingCartIcon,
        DocumentReportIcon,
        ClockIcon,
        CurrencyPoundIcon,
        ChartBarIcon,
        UsersIcon,
        TruckIcon,
        CogIcon,
        ArrowCircleRightIcon,
        LockClosedIcon,
        TemplateIcon,
        InformationCircleIcon,
        TerminalIcon,
        MapIcon,
        UserCircleIcon,
        GlobeAltIcon,
        AcademicCapIcon,
        CheckIcon,
    },
    provide() {
        return {
            sidebar: this.sidebar
        }
    },
    data() {
        return {
            config: getBackendClientConfig(),
            Route,
            user,
            pageTitle: '',
            sidebar: [],
            error: null,
            reportLayouts: [],
            dashboards: [],

            currentPathname: ''
        }
    },
    computed: {
        ...mapStores(useSidebarStore),

        intracompanyTo() {
            return getBackendClientConfig().terminology?.intracompany_to ?? 'To';
        },

        intracompanyFrom() {
            return getBackendClientConfig().terminology?.intracompany_from ?? 'From';
        }
    },
    watch: {
        sidebar() {
            this.sidebarStore.sidebar = [...this.sidebar];
        }
    },
    mounted() {
        this.currentPathname = window.location.pathname;

        if(document.getElementById('page-header-title')) {
            awaitElement('#page-header-title', 25, 10000).then((element) => {
                this.pageTitle = element.innerText.replace(`${Route.id()}: `, '');
            });
        }

        this.sidebarStore.sidebar = [...this.sidebar];
        // this.loadSidebarReportLayouts();
        // this.loadSidebarDashboards();
    },
    methods: {
        capitalize,
        pluralize,
        isClient,
        userCanSee(roles) {
            var canSee = 'hidden';
            if (roles.length) {
                roles.forEach(role => {
                    if (this.user().hasRole(role)) {
                        canSee = 'block';
                    }
                });
            } else {
                canSee = 'block';
            }
            return canSee;
        },
        hideTaskSub(type) {
            if (getBackendClientConfig().tasks?.hidden_pages && getBackendClientConfig().tasks?.hidden_pages.includes(type)) {
                return true;
            }
            return false;
        },
        hasLayouts(reportLayouts) {
            var filtered = Object.values(reportLayouts).filter(layout => {
                return layout.roles.length == 0 || layout.roles.filter(role => {
                    return this.user().hasRole(role);
                }).length > 0;
            });
            return Object.keys(filtered).length > 0;
        },
        openLivechat() {
            document.querySelector('.lz_overlay_wm_button').click();
        },
        // loadSidebarReportLayouts() {
        //     if (this.user().can('list-report-layout') && !window.localDevelopment) {
        //         axios
        //             .get('/api/v2/sidebar-report-layouts')
        //             .then((response) => {
        //                 this.reportLayouts = response.data;
        //             })
        //             .catch((error) => {
        //                 this.error = error;
        //             });
        //     }
        // },
        // loadSidebarDashboards() {
        //     if (!window.localDevelopment) {
        //         axios
        //             .get('/api/v2/users/' + this.user().id + '/sidebar-dashboards')
        //             .then((response) => {
        //                 this.dashboards = response.data;
        //             })
        //             .catch((error) => {
        //                 this.error = error;
        //             });
        //     }
        // },
        groupLayouts(group) {
            return Object.values(this.reportLayouts).filter(layout => {
                return layout.sidebar_group == group;
            });
        }
    }
}
</script>
