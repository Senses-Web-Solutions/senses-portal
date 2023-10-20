import useEcho from '../useEcho';

const echo = useEcho();

export default class {
    constructor(data, id) {
        this.rows = data;
        this.id = id;
        echo.private(
            `tasks.${this.id}.payment-applications`).listen(
            'PaymentApplications\\PaymentApplicationCreated',
            this.taskPaymentApplicationCreated.bind(this)
        );
        echo.private(
            `tasks.${this.id}.payment-applications`).listen(
            'PaymentApplications\\PaymentApplicationUpdated',
            this.taskPaymentApplicationUpdated.bind(this)
        );
        echo.private(
            `tasks.${this.id}.payment-applications`).listen(
            'PaymentApplications\\PaymentApplicationDeleted',
            this.taskPaymentApplicationDeleted.bind(this)
        );
    }

    taskPaymentApplicationCreated(payload) {
        payload.paymentApplication.highlight = true;
        this.rows.data = [payload.paymentApplication].concat(this.rows.data);
    }

    taskPaymentApplicationUpdated(payload) {
        payload.paymentApplication.highlight = true;
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.paymentApplication.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data[index] = payload.paymentApplication;
        }
    }

    taskPaymentApplicationDeleted(payload) {
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.paymentApplication.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data.splice(index, 1);
        }
    }

    destroy() {
        echo.leave(`tasks.${this.id}.payment-applications`);
    }
}
