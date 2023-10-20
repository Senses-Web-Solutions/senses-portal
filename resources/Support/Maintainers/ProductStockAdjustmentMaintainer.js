import useEcho from '../useEcho';

const echo = useEcho();

export default class {
    constructor(data, id) {
        this.rows = data;
        this.id = id;
        echo.private(
            `products.${this.id}.stock-adjustments`).listen(
            'StockAdjustments\\StockAdjustmentCreated',
            this.stockAdjustmentCreated.bind(this)
        );

        echo.private(
            `products.${this.id}.stock-adjustments`).listen(
            'StockAdjustments\\StockAdjustmentUpdated',
            this.stockAdjustmentUpdated.bind(this)
        );

        echo.private(
            `products.${this.id}.stock-adjustments`).listen(
            'StockAdjustments\\StockAdjustmentDeleted',
            this.stockAdjustmentDeleted.bind(this)
        );
    }

    stockAdjustmentCreated(payload) {
        payload.stockAdjustment.highlight = true;
        this.rows.data = [payload.stockAdjustment].concat(this.rows.data);
    }

    stockAdjustmentUpdated(payload) {
        payload.stockAdjustment.highlight = true;
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.stockAdjustment.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data[index] = payload.stockAdjustment;
        }
    }

    stockAdjustmentDeleted(payload) {
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.stockAdjustment.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data.splice(index, 1);
        }
    }

    destroy() {
        echo.leave(`products.${this.id}.stock-adjustments`);
    }
}
