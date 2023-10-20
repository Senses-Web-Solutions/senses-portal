<template>
    <SeMenu :data="data">
        <template #items>
            <MenuItem
                v-for="(menuItem, index) in menuItems"
                :disabled="
                    menuItem.permission
                        ? !user().can(menuItem.permission)
                        : menuItem.disabled
                        ? true
                        : false
                "
                :style="{
                    '--tw-transition-delay': `${
                        index * TransitionTimings.STAGGERED_LIST_DELAY
                    }ms`,
                }"
                :key="`additionalInformation${index}`"
                @click="menuItem.action()"
            >
                <template #icon>
                    <component :is="menuItem.icon" />
                </template>
                {{ menuItem.title }}
            </MenuItem>
        </template>
    </SeMenu>
</template>

<script>
import {
    TrashIcon,
    MailIcon,
    ViewGridIcon,
    CogIcon,
    LockClosedIcon,
    MailOpenIcon,
} from '@heroicons/vue/solid';
import MenuItem from '../../Ui/Menu/MenuItem.vue';
import axios from 'axios';
import TransitionTimings from '../../../Enums/TransitionTimings';
import SeMenu from '../../Ui/Menu/SeMenu.vue';

export default {
    components: {
        MenuItem,
        SeMenu,
        ViewGridIcon,
        CogIcon,
        MailIcon,
        TrashIcon,
        LockClosedIcon,
        MailOpenIcon,
    },
    props: {
        data: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            user: window.user,
            TransitionTimings,
        };
    },

    mounted() {
        this.loadLeaverFormStructure();
    },

    methods: {
        loadLeaverFormStructure() {
            axios
                .get(
                    `/api/v2/form-structures?filter[type]=leaver-form&filter[draft_exact]=0&format=all&sort=-id`
                )
                .then((response) => {
                    this.leaverFormStructureId =
                        Object.keys(response.data).length > 0
                            ? response.data[0].id
                            : null;
                })
                .catch((error) => {
                    console.log(error);
                    this.error = error;
                    this.$notifications.push({
                        title: 'Load failed',
                        description: error.response.data.message,
                        type: 'danger',
                    });
                });
        },
        markUserAsLeft() {
            if (this.user().can('create-form') && this.leaverFormStructureId) {
                this.$asides.push('Form', {
                    formStructureId: this.leaverFormStructureId,
                    formableType: 'user',
                    formableId: this.data.id,
                });
            } else {
                this.$notifications.push({
                    title: 'Error',
                    description:
                        'Cannot load leaver form due to permissions or the correct form structure not existing.',
                    type: 'danger',
                });
            }
        },
        async markUserAsReturned() {
            const confirmed = await this.$dialogs.danger(
                'Are you sure?',
                "This will clear the user's end date and allow them to be listed and assigned."
            );

            if (confirmed) {
                axios
                    .post(`/api/v2/users/${this.data.id}/mark-as-returned`)
                    .then(() => {
                        this.$notifications.push({
                            title: 'User updated',
                            description:
                                'User has been marked as returned to work.',
                            type: 'success',
                        });
                    })
                    .catch((error) => {
                        this.error = error;
                        this.$notifications.push({
                            title: 'Error',
                            description: error.response.data.message,
                            type: 'danger',
                        });
                    });
            }
        },
        sendRegistrationEmail() {
            axios
                .post(`/api/v2/users/${this.data.id}/send-registration-email`)
                .then(() => {
                    this.$notifications.push({
                        title: 'Email sent',
                        description:
                            'The user will receive an email with a link to complete their registration.',
                        type: 'success',
                    });
                })
                .catch((error) => {
                    this.error = error;
                    this.$notifications.push({
                        title: 'Email failed',
                        description: error.response.data.message,
                        type: 'danger',
                    });
                });
        },
    },
    computed: {
        menuItems() {
            if (this.data.user_type?.slug == 'public') {
                var menuItems = [
                    {
                        title: 'Send Registration Email',
                        permission: 'create-client-application',
                        action: () => this.sendRegistrationEmail(),
                        icon: 'MailIcon',
                    },
                ];
            } else {
                var menuItems = [];
            }

            menuItems.push({
                title: 'Update Layout',
                disabled: !this.userCanUpdateLayout,
                action: () =>
                    this.userCanUpdateLayout
                        ? this.$asides.push('UserLayoutForm', this.data)
                        : null,
                icon: 'ViewGridIcon',
            });

            menuItems.push({
                title: 'Update Contact Info',
                action: () =>
                    this.$asides.push('UserContactSettingsForm', this.data),
                icon: 'MailOpenIcon',
            });

            menuItems.push({
                title: 'Update Password',
                disabled: !this.userCanUpdatePassword,
                action: () =>
                    this.userCanUpdatePassword
                        ? this.$asides.push('UserPasswordForm', this.data)
                        : null,
                icon: 'LockClosedIcon',
            });

            menuItems.push({
                title: 'Update Admin Info',
                disabled: !this.userCanUpdateAdmin,
                action: () =>
                    this.userCanUpdateAdmin
                        ? this.$asides.push('UserAdminForm', this.data)
                        : null,
                icon: 'CogIcon',
            });

            if (this.data.end_date == null) {
                menuItems.push({
                    title: 'Mark as Leaver',
                    disabled: !this.user().can('delete-user'),
                    action: () => this.markUserAsLeft(),
                    icon: 'TrashIcon',
                });
            } else {
                menuItems.push({
                    title: 'Mark as Returned',
                    disabled: !this.user().can('update-user'),
                    action: () => this.markUserAsReturned(),
                    icon: 'TrashIcon',
                });
            }

            return menuItems;
        },
        userCanUpdatePassword() {
            if (
                this.user().can(`update-user-password`) ||
                (this.user().can(`update-own-user-password`) &&
                    this.data.id === this.user().id) ||
                (this.user().managed_user_ids &&
                    this.user().can(`update-managed-user-password`) &&
                    this.user().managed_user_ids.includes(this.data.id))
            ) {
                return true;
            }
            return false;
        },
        userCanUpdateAdmin() {
            if (
                this.user().can(`update-user-admin`) ||
                (this.user().can(`update-own-user-admin`) &&
                    this.data.id === this.user().id) ||
                (this.user().managed_user_ids &&
                    this.user().can(`update-managed-user-admin`) &&
                    this.user().managed_user_ids.includes(this.data.id))
            ) {
                return true;
            }
            return false;
        },
        userCanUpdateLayout() {
            if (
                this.user().can(`update-user-layout`) ||
                (this.user().can(`update-own-user-layout`) &&
                    this.data.id === this.user().id) ||
                (this.user().managed_user_ids &&
                    this.user().can(`update-managed-user-layout`) &&
                    this.user().managed_user_ids.includes(this.data.id))
            ) {
                return true;
            }
            return false;
        },
        userCanDelete() {
            if (
                this.user().can(`delete-user`) ||
                (this.user().can(`delete-own-user`) &&
                    this.data.id === this.user().id) ||
                (this.user().managed_user_ids &&
                    this.user().can(`delete-managed-user`) &&
                    this.user().managed_user_ids.includes(this.data.id))
            ) {
                return true;
            }
            return false;
        },
    },
};
</script>
