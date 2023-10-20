import useEcho from '../useEcho';

const echo = useEcho();

export default class {
    constructor(data, id) {
        this.rows = data;
        this.id = id;

        echo.private(`tasks.${this.id}.forms`).listen('Form\\FormCreated', this.created.bind(this));
        echo.private(`tasks.${this.id}.forms`).listen('Form\\FormUpdated', this.updated.bind(this));
        echo.private(`tasks.${this.id}.forms`).listen('Form\\FormDeleted', this.deleted.bind(this));
    }

    created (payload) {
        payload.form.highlight = true;
        this.rows.data = [payload.form].concat(this.rows.data);
        console.log("CREATED");
    }

    updated (payload) {
        payload.form.highlight = true;
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.form.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data[index] = payload.form;
        }
        console.log("UPDATED");
    }

    deleted (payload) {
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.form.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data.splice(index, 1);
        }
        console.log("DELETED");
    }

    destroy () {
        echo.leave(`tasks.${this.id}.forms`);
    }
}
