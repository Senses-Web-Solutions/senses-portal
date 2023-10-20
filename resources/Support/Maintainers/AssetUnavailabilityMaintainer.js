import useEcho from '../useEcho';

const echo = useEcho();

export default class {
    constructor(data, id) {
        this.rows = data;
        this.id = id;
        echo.private(`assets.${this.id}.unavailabilities`).listen(
            'Unavailabilities\\UnavailabilityCreated',
            this.created.bind(this)
        );
        echo.private(`assets.${this.id}.unavailabilities`).listen(
            'Unavailabilities\\UnavailabilityUpdated',
            this.updated.bind(this)
        );
        echo.private(`assets.${this.id}.unavailabilities`).listen(
            'Unavailabilities\\UnavailabilityDeleted',
            this.deleted.bind(this)
        );
    }

    created(payload) {
        payload.assetUnavailability.highlight = true;
        this.rows.data = [payload.assetUnavailability].concat(this.rows.data);
    }

    updated(payload) {
        payload.assetUnavailability.highlight = true;
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.assetUnavailability.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data[index] = payload.assetUnavailability;
        }
    }

    deleted(payload) {
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.assetUnavailability.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data.splice(index, 1);
        }
    }

    destroy() {
        echo.leave(`assets.${this.id}.unavailabilities`);
    }
}
