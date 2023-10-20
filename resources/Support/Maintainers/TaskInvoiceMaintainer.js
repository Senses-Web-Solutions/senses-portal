import useEcho from '../useEcho';

const echo = useEcho();

export default class {
    constructor(data, id) {
        this.rows = data;
        this.id = id;
        echo.private(`tasks.${this.id}.invoices`).listen(
            'Invoices\\InvoiceCreated',
            this.taskInvoiceCreated.bind(this)
        );
        echo.private(`tasks.${this.id}.invoices`).listen(
            'Invoices\\InvoiceUpdated',
            this.taskInvoiceUpdated.bind(this)
        );
        echo.private(`tasks.${this.id}.invoices`).listen(
            'Invoices\\InvoiceDeleted',
            this.taskInvoiceDeleted.bind(this)
        );
    }

    taskInvoiceCreated(payload) {
        payload.invoice.highlight = true;
        if (this.rows.data) {
            this.rows.data = [payload.invoice].concat(this.rows.data);
        }
    }

    taskInvoiceUpdated(payload) {
        payload.invoice.highlight = true;
        if (this.rows.data) {
            const index = this.rows.data.findIndex(
                (item) => item.id === payload.invoice.id
            );
            if (index > -1 && this.rows.data[index]) {
                this.rows.data[index] = payload.invoice;
            }
        }
    }

    taskInvoiceDeleted(payload) {
        if (this.rows.data) {
            const index = this.rows.data.findIndex(
                (item) => item.id === payload.invoice.id
            );
            if (index > -1 && this.rows.data[index]) {
                this.rows.data.splice(index, 1);
            }
        }
    }

    destroy() {
        echo.leave(`tasks.${this.id}.invoices`);
    }
}
