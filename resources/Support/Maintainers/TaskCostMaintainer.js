import useEcho from '../useEcho';

const echo = useEcho();

export default class {
    constructor(data, id) {
        this.rows = data;
        this.id = id;
        echo.private(
            `tasks.${this.id}.costs`).listen(
            'Revenues\\RevenueCreated',
            this.taskCostCreated.bind(this)
        );

        echo.private(
            `tasks.${this.id}.costs`).listen(
            'Revenues\\RevenueUpdated',
            this.taskCostUpdated.bind(this)
        );

        echo.private(
            `tasks.${this.id}.costs`).listen(
            'Revenues\\RevenueDeleted',
            this.taskCostDeleted.bind(this)
        );
    }

    taskCostCreated(payload) {
        payload.revenue.highlight = true;
        this.rows.data = [payload.revenue].concat(this.rows.data);
    }

    taskCostUpdated(payload) {
        payload.revenue.highlight = true;
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.revenue.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data[index] = payload.revenue;
        }
    }

    taskCostDeleted(payload) {
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.revenue.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data.splice(index, 1);
        }
    }

    destroy() {
        echo.leave(`tasks.${this.id}.costs`);
    }
}
