import useEcho from '../useEcho';

const echo = useEcho();

export default class {
    constructor(data, id) {
        this.rows = data;
        this.id = id;
        echo.private(
            `products.${this.id}.stock-product-assignments`).listen(
            'StockProductAssignments\\StockProductAssignmentCreated',
            this.created.bind(this)
        );
        echo.private(
            `products.${this.id}.stock-product-assignments`).listen(
            'StockProductAssignments\\StockProductAssignmentUpdated',
            this.updated.bind(this)
        );
        echo.private(
            `products.${this.id}.stock-product-assignments`).listen(
            'StockProductAssignments\\StockProductAssignmentDeleted',
            this.deleted.bind(this)
        );
    }

    created (payload) {
        console.log(payload);
        payload.stockProductAssignment.highlight = true;
        this.rows.data = [payload.stockProductAssignment].concat(this.rows.data);
    }

    updated (payload) {
        console.log(payload);
        payload.stockProductAssignment.highlight = true;
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.stockProductAssignment.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data[index] = payload.stockProductAssignment;
        }
    }

    deleted (payload) {
        console.log(payload);
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.stockProductAssignment.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data.splice(index, 1);
        }
    }

    destroy () {
        echo.leave(`products.${this.id}.stock-product-assignments`);
    }
}
