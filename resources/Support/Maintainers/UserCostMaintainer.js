import useEcho from '../useEcho';

const echo = useEcho();

export default class {
    constructor(data, id) {
        this.rows = data;
        this.id = id;
        echo.private(`users.${this.id}.costs`).listen(
            'Revenues\\RevenueCreated',
            this.userCostCreated.bind(this)
        );

        echo.private(`users.${this.id}.costs`).listen(
            'Revenues\\RevenueUpdated',
            this.userCostUpdated.bind(this)
        );

        echo.private(`users.${this.id}.costs`).listen(
            'Revenues\\RevenueDeleted',
            this.userCostDeleted.bind(this)
        );
    }

    userCostCreated(payload) {
        payload.revenue.highlight = true;
        this.rows.data = [payload.revenue].concat(this.rows.data);
    }

    userCostUpdated(payload) {
        payload.revenue.highlight = true;
        const index = this.rows.findIndex(
            (item) => item.id === payload.revenue.id
        );
        if (index > -1 && this.rows[index]) {
            this.rows[index] = payload.revenue;
        }
    }

    userCostDeleted(payload) {
        const index = this.rows.findIndex(
            (item) => item.id === payload.revenue.id
        );
        if (index > -1 && this.rows[index]) {
            this.rows.splice(index, 1);
        }
    }

    destroy() {
        echo.leave(`users.${this.id}.costs`);
    }
}
