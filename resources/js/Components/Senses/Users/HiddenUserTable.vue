<template>
    <SensesTable :url="url" :fields="fields" table="users" :actions="actions" setting="table-users" event="user-updated" @row-click="rowClick" @action-applied="actionApplied">
        <template #colour="slotProps">
            <td :class="slotProps.class">
                <Colour :colour="slotProps.row.colour"></Colour>
            </td>
        </template>
        <template #roles="slotProps">
            <td :class="slotProps.class">
                {{ slotProps.row.roles?.map((role) => role.title).join(', ') }}
            </td>
        </template>
        <template #depots="slotProps">
            <td :class="slotProps.class">
                {{ slotProps.row.depots?.map((role) => role.title).join(', ') }}
            </td>
        </template>
        <template #departments="slotProps">
            <td :class="slotProps.class">
                {{
                        slotProps.row.departments
                            ?.map((role) => role.title)
                            .join(', ')
                }}
            </td>
        </template>
        <template #notes="slotProps">
            <td :class="slotProps.class">
                <div v-if="slotProps.row.notes" v-html="slotProps.row.notes.replaceAll('<br>', ' ').replaceAll('<br/>', ' ')"></div>
            </td>
        </template>
    </SensesTable>
</template>

<script>
import SensesTable from '../Tables/SensesTable.vue';
import Colour from '../../Ui/Colour.vue';

export default {
    components: {
        SensesTable,
        Colour,
    },

    props: {
        url: {
            type: String,
            default: '/api/v2/hidden-users'
        }
    },

    data () {
        return {
            user: window.user,
            actions: [],
            fields: [
                { label: 'ID', key: 'id' },
                { label: 'Name', key: 'full_name' },
                { label: 'Email', key: 'email' },
                // { label: 'Telephone', key: 'telephone' },
                { label: 'Mobile', key: 'mobile' },
                { label: 'Address', key: 'homeVenue.street' },
                { label: 'Postcode', key: 'homeVenue.postcode' },
                {
                    label: 'Roles',
                    key: 'roles',
                    query: 'template',
                    includes: ['roles'],
                    sort: false,
                    filter: false,
                },
                { label: 'User Type', key: 'userType.title' },
                {
                    label: 'Company',
                    key: 'company.title',
                },
                {
                    label: 'Depots',
                    key: 'depots',
                    query: 'template',
                    includes: ['depots'],
                    sort: false,
                    filter: false,
                },
                {
                    label: 'Departments',
                    key: 'departments',
                    query: 'template',
                    includes: ['departments'],
                    sort: false,
                    filter: false,
                },
                {
                    label: 'Owned By',
                    key: 'currentUserOwnership.depot.title',
                    sort: false,
                },
                { label: 'Notes', key: 'notes' },
                // { label: 'Colour', key: 'colour' },
                {
                    label: 'Hidden At',
                    key: 'hidden_at',
                    filter: { type: 'datetime' },
                    format: 'datetime',
                },
                {
                    label: 'Hidden By',
                    key: 'hider.full_name',
                },
            ],
        };
    },

    methods: {
        rowClick (row, type) {
            if (row.id) {
                if (
                    this.user().can('show-user') ||
                    (this.user().can('show-own-user') &&
                        row.id === this.user().id) ||
                    (this.user().can('show-managed-user') &&
                        this.user().managed_user_ids.includes(row.id))
                ) {
                    if (type && type === 'blank') {
                        window.open(`/users/${row.id}`);
                    } else {
                        window.location.href = `/users/${row.id}`;
                    }
                }
            }
        },
        actionApplied (type, rows) {
            console.log(type, rows);
        },
    },
};
</script>

//Generated 23-09-2021 09:47:48
