import useEcho from '../useEcho';

const echo = useEcho();

export default class {
    constructor(data, id) {
        this.rows = data;
        this.id = id;
        echo.private(
            `products.${this.id}.product-groups`).listen(
            'ProductGroups\\ProductGroupCreated',
            this.productGroupCreated.bind(this)
        );

        echo.private(
            `products.${this.id}.product-groups`).listen(
            'ProductGroups\\ProductGroupUpdated',
            this.productGroupUpdated.bind(this)
        );

        echo.private(
            `products.${this.id}.product-groups`).listen(
            'ProductGroups\\ProductGroupDeleted',
            this.productGroupDeleted.bind(this)
        );
    }

    productGroupCreated(payload) {
        payload.productGroup.highlight = true;
        this.rows.data = [payload.productGroup].concat(this.rows.data);
    }

    productGroupUpdated(payload) {
        payload.productGroup.highlight = true;
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.productGroup.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data[index] = payload.productGroup;
        }
    }

    productGroupDeleted(payload) {
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.productGroup.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data.splice(index, 1);
        }
    }

    destroy() {
        echo.leave(`products.${this.id}.product-groups`);
    }
}
