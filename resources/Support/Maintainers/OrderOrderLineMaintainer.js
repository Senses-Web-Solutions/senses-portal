import useEcho from '../useEcho';

const echo = useEcho();

export default class {
    constructor(data, id) {
        this.rows = data;
        this.id = id;
        echo.private(
            `orders.${this.id}.order-lines`).listen(
            'OrderLines\\OrderLineCreated',
            this.orderOrderLineCreated.bind(this)
        );
        echo.private(
            `orders.${this.id}.order-lines`).listen(
            'OrderLines\\OrderLineUpdated',
            this.orderOrderLineUpdated.bind(this)
        );
        echo.private(
            `orders.${this.id}.order-lines`).listen(
            'OrderLines\\OrderLineDeleted',
            this.orderOrderLineDeleted.bind(this)
        );
    }

    orderOrderLineCreated(payload) {
        payload.orderLine.highlight = true;
        this.rows.data = [payload.orderLine].concat(this.rows.data);
    }

    orderOrderLineUpdated(payload) {
        payload.orderLine.highlight = true;

        const index = this.rows.data.findIndex(
            (item) => item.id === payload.orderLine.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data[index] = payload.orderLine;
        }
    }

    orderOrderLineDeleted(payload) {
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.orderLine.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data.splice(index, 1);
        }
    }

    destroy() {
        echo.leave(`orders.${this.id}.order-lines`);
    }
}
