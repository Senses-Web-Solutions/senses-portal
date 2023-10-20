export default function user() {
    if (!window.sensesCurrentUser) {
        return {
            can: () => false,
            hasRole: () => false
        };
    }

    return {
        ...window.sensesCurrentUser,
        can(ability) {
            if (this.abilities && this.abilities.length) {
                return !!this.abilities.find((item) => item.name === ability);
            }
            return false;
        },
        hasRole(role) {
            if (this.roles && this.roles.length) {
                return this.roles.indexOf(role) !== -1;
            }
            return false;
        },
    };
}

// <div v-if="user() && user().can('update tasks')"></div>
