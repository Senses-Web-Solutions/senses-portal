import useEcho from '../useEcho';

const echo = useEcho();

export default class {
    constructor(data, id) {
        this.rows = data;
        this.id = id;
        echo.private(
            `tasks.${this.id}.client-applications`).listen(
            'ClientApplications\\ClientApplicationCreated',
            this.taskClientApplicationCreated.bind(this)
        );
        echo.private(
            `tasks.${this.id}.client-applications`).listen(
            'ClientApplications\\ClientApplicationUpdated',
            this.taskClientApplicationUpdated.bind(this)
        );
        echo.private(
            `tasks.${this.id}.client-applications`).listen(
            'ClientApplications\\ClientApplicationDeleted',
            this.taskClientApplicationDeleted.bind(this)
        );
    }

    taskClientApplicationCreated(payload) {
        payload.clientApplication.highlight = true;
        this.rows.data = [payload.clientApplication].concat(this.rows.data);
    }

    taskClientApplicationUpdated(payload) {
        payload.clientApplication.highlight = true;
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.clientApplication.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data[index] = payload.clientApplication;
        }
    }

    taskClientApplicationDeleted(payload) {
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.clientApplication.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data.splice(index, 1);
        }
    }

    destroy() {
        echo.leave(`tasks.${this.id}.client-applications`);
    }
}
