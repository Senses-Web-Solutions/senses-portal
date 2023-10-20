import useEcho from '../useEcho';

const echo = useEcho();

export default class {
    constructor(data, id) {
        this.rows = data;
        this.id = id;
        echo.private(
            `tasks.${this.id}.product-requests`).listen(
                'ProductRequests\\ProductRequestCreated',
                this.created.bind(this)
            );
        echo.private(
            `tasks.${this.id}.product-requests`).listen(
                'ProductRequests\\ProductRequestUpdated',
                this.updated.bind(this)
            );
        echo.private(
            `tasks.${this.id}.product-requests`).listen(
                'ProductRequests\\ProductRequestDeleted',
                this.deleted.bind(this)
            );
    }

    created (payload) {
        payload.productRequest.highlight = true;
        this.rows.data = [payload.productRequest].concat(this.rows.data);
    }

    updated (payload) {
        payload.productRequest.highlight = true;
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.productRequest.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data[index] = payload.productRequest;
        }
    }

    deleted (payload) {
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.productRequest.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data.splice(index, 1);
        }
    }

    destroy () {
        echo.leave(`tasks.${this.id}.product-requests`);
    }
}
