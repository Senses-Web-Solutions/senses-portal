import useEcho from '../useEcho';

const echo = useEcho();

export default class {
    constructor(data, id) {
        this.rows = data;
        this.id = id;
        echo.private(
            `companies.${this.id}.quotes`).listen(
            'Quotes\\QuoteCreated',
            this.companyQuoteCreated.bind(this)
        );
        echo.private(
            `companies.${this.id}.quotes`).listen(
            'Quotes\\QuoteUpdated',
            this.companyQuoteUpdated.bind(this)
        );
        echo.private(
            `companies.${this.id}.quotes`).listen(
            'Quotes\\QuoteDeleted',
            this.companyQuoteDeleted.bind(this)
        );
    }

    companyQuoteCreated(payload) {
        payload.quote.highlight = true;
        this.rows.data = [payload.quote].concat(this.rows.data);
    }

    companyQuoteUpdated(payload) {
        payload.quote.highlight = true;
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.quote.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data[index] = payload.quote;
        }
    }

    companyQuoteDeleted(payload) {
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.quote.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data.splice(index, 1);
        }
    }

    destroy() {
        echo.leave(`companies.${this.id}.quotes`);
    }
}
