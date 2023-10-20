import useEcho from '../useEcho';

const echo = useEcho();

export default class {
    constructor(data, id) {
        this.rows = data;
        this.id = id;
        echo.private(
            `products.${this.id}.order-lines`).listen(
            'OrderLines\\OrderLineCreated',
            this.orderLineCreated.bind(this)
        );

        echo.private(
            `products.${this.id}.order-lines`).listen(
            'OrderLines\\OrderLineUpdated',
            this.orderLineUpdated.bind(this)
        );

        echo.private(
            `products.${this.id}.order-lines`).listen(
            'OrderLines\\OrderLineDeleted',
            this.orderLineDeleted.bind(this)
        );
    }

    orderLineCreated(payload) {
        payload.orderLine.highlight = true;
        this.rows.data = [payload.orderLine].concat(this.rows.data);
    }

    orderLineUpdated(payload) {
        payload.orderLine.highlight = true;
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.orderLine.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data[index] = payload.orderLine;
        }
    }

    orderLineDeleted(payload) {
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.orderLine.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data.splice(index, 1);
        }
    }

    destroy() {
        echo.leave(`products.${this.id}.order-lines`);
    }
}
