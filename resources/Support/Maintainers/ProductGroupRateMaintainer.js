import useEcho from '../useEcho';

const echo = useEcho();

export default class {
    constructor(data, id) {
        this.rows = data;
        this.id = id;
        echo.private(
            `product-groups.${this.id}.rates`).listen(
            'Rates\\RateCreated',
            this.rateCreated.bind(this)
        );

        echo.private(
            `product-groups.${this.id}.rates`).listen(
            'Rates\\RateUpdated',
            this.rateUpdated.bind(this)
        );

        echo.private(
            `product-groups.${this.id}.rates`).listen(
            'Rates\\RateDeleted',
            this.rateDeleted.bind(this)
        );
    }

    rateCreated(payload) {
        payload.rate.highlight = true;
        this.rows.data = [payload.rate].concat(this.rows.data);
    }

    rateUpdated(payload) {
        payload.rate.highlight = true;
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.rate.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data[index] = payload.rate;
        }
    }

    rateDeleted(payload) {
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.rate.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data.splice(index, 1);
        }
    }

    destroy() {
        echo.leave(`product-groups.${this.id}.rates`);
    }
}
