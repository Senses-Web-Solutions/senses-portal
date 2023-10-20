import useEcho from '../useEcho';

const echo = useEcho();

export default class {
    constructor(data, id) {
        this.rows = data;
        this.id = id;
        echo.private(
            `client-application-types.${this.id}.client-applications`).listen(
            'ClientApplications\\ClientApplicationCreated',
            this.created.bind(this)
        );
        echo.private(
            `client-application-types.${this.id}.client-applications`).listen(
            'ClientApplications\\ClientApplicationUpdated',
            this.updated.bind(this)
        );
        echo.private(
            `client-application-types.${this.id}.client-applications`).listen(
            'ClientApplications\\ClientApplicationDeleted',
            this.deleted.bind(this)
        );
    }

    created (payload) {
        payload.clientApplication.highlight = true;
        this.rows.data = [payload.clientApplication].concat(this.rows.data);
    }

    updated (payload) {
        payload.clientApplication.highlight = true;
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.clientApplication.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data[index] = payload.clientApplication;
        }
    }

    deleted (payload) {
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.clientApplication.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data.splice(index, 1);
        }
    }

    destroy () {
        echo.leave(`client-application-types.${this.id}.client-applications`);
    }
}
