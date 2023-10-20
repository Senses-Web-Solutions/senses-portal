import useEcho from '../useEcho';

const echo = useEcho();

export default class {
    constructor(data, id) {
        this.rows = data;
        this.id = id;
        echo.private(
            `companies.${this.id}.orders`).listen(
            'Orders\\OrderCreated',
            this.orderCreated.bind(this)
        );

        echo.private(
            `companies.${this.id}.orders`).listen(
            'Orders\\OrderUpdated',
            this.orderUpdated.bind(this)
        );

        echo.private(
            `companies.${this.id}.orders`).listen(
            'Orders\\OrderDeleted',
            this.orderDeleted.bind(this)
        );
    }

    orderCreated(payload) {
        payload.order.highlight = true;
        this.rows.data = [payload.order].concat(this.rows.data);
    }

    orderUpdated(payload) {
        payload.order.highlight = true;
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.order.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data[index] = payload.order;
        }
    }

    orderDeleted(payload) {
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.order.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data.splice(index, 1);
        }
    }

    destroy() {
        echo.leave(`companies.${this.id}.orders`);
    }
}
