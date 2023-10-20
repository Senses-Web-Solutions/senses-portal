import useEcho from '../useEcho';

const echo = useEcho();

export default class {
    constructor(data, id) {
        this.rows = data;
        this.id = id;
        echo.private(
            `products.${this.id}.stock-products`).listen(
            'StockProducts\\StockProductCreated',
            this.stockProductCreated.bind(this)
        );

        echo.private(
            `products.${this.id}.stock-products`).listen(
            'StockProducts\\StockProductUpdated',
            this.stockProductUpdated.bind(this)
        );

        echo.private(
            `products.${this.id}.stock-products`).listen(
            'StockProducts\\StockProductDeleted',
            this.stockProductDeleted.bind(this)
        );
    }

    stockProductCreated(payload) {

        payload.stockProduct.highlight = true;
        this.rows.data = [payload.stockProduct].concat(this.rows.data);
    }

    stockProductUpdated(payload) {
        payload.stockProduct.highlight = true;
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.stockProduct.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data[index] = payload.stockProduct;
        }
    }

    stockProductDeleted(payload) {
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.stockProduct.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data.splice(index, 1);
        }
    }

    destroy() {
        echo.leave(`products.${this.id}.stock-products`);
    }
}
