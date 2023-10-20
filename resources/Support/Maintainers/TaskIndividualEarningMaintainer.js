import useEcho from '../useEcho';

const echo = useEcho();

export default class {
    constructor(data, id) {
        this.rows = data;
        this.id = id;
        echo.private(
            `tasks.${this.id}.individual-earnings`).listen(
            'Revenues\\RevenueCreated',
            this.created.bind(this)
        );
        echo.private(
            `tasks.${this.id}.individual-earnings`).listen(
            'Revenues\\RevenueUpdated',
            this.updated.bind(this)
        );
        echo.private(
            `tasks.${this.id}.individual-earnings`).listen(
            'Revenues\\RevenueDeleted',
            this.deleted.bind(this)
        );
    }

    created (payload) {
        payload.revenue.highlight = true;
        this.rows.data = [payload.revenue].concat(this.rows.data);
    }

    updated (payload) {
        payload.revenue.highlight = true;
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.revenue.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data[index] = payload.revenue;
        }
    }

    deleted (payload) {
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.revenue.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data.splice(index, 1);
        }
    }

    destroy () {
        echo.leave(`tasks.${this.id}.individual-earnings`);
    }
}
