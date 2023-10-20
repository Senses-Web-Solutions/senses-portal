import useEcho from '../useEcho';

const echo = useEcho();

export default class {
    constructor(data, id) {
        this.rows = data;
        this.id = id;
        echo.private(
            `assets.${this.id}.earnings`).listen(
            'Revenues\\RevenueCreated',
            this.earningCreated.bind(this)
        );

        echo.private(
            `assets.${this.id}.earnings`).listen(
            'Revenues\\RevenueUpdated',
            this.earningUpdated.bind(this)
        );

        echo.private(
            `assets.${this.id}.earnings`).listen(
            'Revenues\\RevenueDeleted',
            this.earningDeleted.bind(this)
        );
    }

    earningCreated(payload) {
        payload.revenue.highlight = true;
        this.rows.data = [payload.revenue].concat(this.rows.data);
    }

    earningUpdated(payload) {
        payload.revenue.highlight = true;
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.revenue.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data[index] = payload.revenue;
        }
    }

    earningDeleted(payload) {
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.revenue.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data.splice(index, 1);
        }
    }

    destroy() {
        echo.leave(`assets.${this.id}.earnings`);
    }
}
