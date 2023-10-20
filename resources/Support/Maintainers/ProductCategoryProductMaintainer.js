import useEcho from '../useEcho';

const echo = useEcho();

export default class {
    constructor(data, id) {
        this.rows = data;
        this.id = id;
        echo.private(
            `product-categories.${this.id}.products`).listen(
            'Products\\ProductCreated',
            this.productCreated.bind(this)
        );

        echo.private(
            `product-categories.${this.id}.products`).listen(
            'Products\\ProductUpdated',
            this.productUpdated.bind(this)
        );

        echo.private(
            `product-categories.${this.id}.products`).listen(
            'Products\\ProductDeleted',
            this.productDeleted.bind(this)
        );
    }

    productCreated(payload) {
        payload.product.highlight = true;
        this.rows.data = [payload.product].concat(this.rows.data);
    }

    productUpdated(payload) {
        payload.product.highlight = true;
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.product.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data[index] = payload.product;
        }
    }

    productDeleted(payload) {
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.product.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data.splice(index, 1);
        }
    }

    destroy() {
        echo.leave(`product-categories.${this.id}.products`);
    }
}
