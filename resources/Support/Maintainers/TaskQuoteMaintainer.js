import useEcho from '../useEcho';

const echo = useEcho();

export default class {
    constructor(data, id) {
        this.rows = data;
        this.id = id;
        echo.private(
            `tasks.${this.id}.quotes`).listen(
            'Quotes\\QuoteCreated',
            this.taskQuoteCreated.bind(this)
        );
        echo.private(
            `tasks.${this.id}.quotes`).listen(
            'Quotes\\QuoteUpdated',
            this.taskQuoteUpdated.bind(this)
        );
        echo.private(
            `tasks.${this.id}.quotes`).listen(
            'Quotes\\QuoteDeleted',
            this.taskQuoteDeleted.bind(this)
        );
    }

    taskQuoteCreated(payload) {
        payload.quote.highlight = true;
        this.rows.data = [payload.quote].concat(this.rows.data);
    }

    taskQuoteUpdated(payload) {
        payload.quote.highlight = true;
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.quote.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data[index] = payload.quote;
        }
    }

    taskQuoteDeleted(payload) {
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.quote.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data.splice(index, 1);
        }
    }

    destroy() {
        echo.leave(`tasks.${this.id}.quotes`);
    }
}
