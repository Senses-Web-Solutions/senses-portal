import useEcho from '../useEcho';

const echo = useEcho();

export default class {
    constructor(data, id) {
        this.rows = data;
        this.id = id;
        echo.private(
            `tasks.${this.id}.assignment-group-login-statuses`).listen(
                'AssignmentGroupLoginStatuses\\AssignmentGroupLoginStatusCreated',
                this.created.bind(this)
            );
        echo.private(
            `tasks.${this.id}.assignment-group-login-statuses`).listen(
                'AssignmentGroupLoginStatuses\\AssignmentGroupLoginStatusUpdated',
                this.updated.bind(this)
            );
        echo.private(
            `tasks.${this.id}.assignment-group-login-statuses`).listen(
                'AssignmentGroupLoginStatuses\\AssignmentGroupLoginStatusDeleted',
                this.deleted.bind(this)
            );
    }

    created (payload) {
        payload.assignmentGroupLoginStatus.highlight = true;
        this.rows.data = [payload.assignmentGroupLoginStatus].concat(this.rows.data);
    }

    updated (payload) {
        payload.assignmentGroupLoginStatus.highlight = true;
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.assignmentGroupLoginStatus.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data[index] = payload.assignmentGroupLoginStatus;
        }
    }

    deleted (payload) {
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.assignmentGroupLoginStatus.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data.splice(index, 1);
        }
    }

    destroy () {
        echo.leave(`tasks.${this.id}.assignment-group-login-statuses`);
    }
}
