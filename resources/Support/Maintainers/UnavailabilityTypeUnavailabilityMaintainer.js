import useEcho from '../useEcho';

const echo = useEcho();

export default class {
    constructor(data, id) {
        this.rows = data;
        this.id = id;
        echo.private(
            `unavailability-types.${this.id}.unavailabilities`).listen(
            'Unavailabilities\\UnavailabilityCreated',
            this.created.bind(this)
        );
        echo.private(
            `unavailability-types.${this.id}.unavailabilities`).listen(
            'Unavailabilities\\UnavailabilityUpdated',
            this.updated.bind(this)
        );
        echo.private(
            `unavailability-types.${this.id}.unavailabilities`).listen(
            'Unavailabilities\\UnavailabilityDeleted',
            this.deleted.bind(this)
        );
    }

    created (payload) {
        payload.unavailability.highlight = true;
        this.rows.data = [payload.unavailability].concat(this.rows.data);
    }

    updated (payload) {
        payload.unavailability.highlight = true;
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.unavailability.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data[index] = payload.unavailability;
        }
    }

    deleted (payload) {
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.unavailability.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data.splice(index, 1);
        }
    }

    destroy () {
        echo.leave(`unavailability-types.${this.id}.unavailabilities`);
    }
}
