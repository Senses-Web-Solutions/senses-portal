import useEcho from '../useEcho';

const echo = useEcho();

export default class {
    constructor(data, id) {
        this.rows = data;
        this.id = id;
        echo.private(
            `assets.${this.id}.faults`).listen(
                'Faults\\FaultCreated',
                this.created.bind(this)
            );
        echo.private(
            `assets.${this.id}.faults`).listen(
                'Faults\\FaultUpdated',
                this.updated.bind(this)
            );
        echo.private(
            `assets.${this.id}.faults`).listen(
                'Faults\\FaultDeleted',
                this.deleted.bind(this)
            );
    }

    created (payload) {
        payload.fault.highlight = true;
        this.rows.data = [payload.fault].concat(this.rows.data);
    }

    updated (payload) {
        payload.fault.highlight = true;
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.fault.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data[index] = payload.fault;
        }
    }

    deleted (payload) {
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.fault.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data.splice(index, 1);
        }
    }

    destroy () {
        echo.leave(`assets.${this.id}.faults`);
    }
}
