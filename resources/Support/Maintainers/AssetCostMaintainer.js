import useEcho from '../useEcho';

const echo = useEcho();

export default class {
    constructor(data, id) {
        this.rows = data;
        this.id = id;
        echo.private(
            `assets.${this.id}.costs`).listen(
            'Revenues\\RevenueCreated',
            this.costCreated.bind(this)
        );

        echo.private(
            `assets.${this.id}.costs`).listen(
            'Revenues\\RevenueUpdated',
            this.costUpdated.bind(this)
        );

        echo.private(
            `assets.${this.id}.costs`).listen(
            'Revenues\\RevenueDeleted',
            this.costDeleted.bind(this)
        );
    }

    costCreated(payload) {
        payload.revenue.highlight = true;
        this.rows.data = [payload.revenue].concat(this.rows.data);
    }

    costUpdated(payload) {
        payload.revenue.highlight = true;
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.revenue.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data[index] = payload.revenue;
        }
    }

    costDeleted(payload) {
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.revenue.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data.splice(index, 1);
        }
    }

    destroy() {
        echo.leave(`assets.${this.id}.costs`);
    }
}
