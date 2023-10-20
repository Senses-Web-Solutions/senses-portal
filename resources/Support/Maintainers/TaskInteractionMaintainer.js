import useEcho from '../useEcho';

const echo = useEcho();

export default class {
    constructor(data, id) {
        this.rows = data;
        this.id = id;
        echo.private(
            `tasks.${this.id}.interactions`).listen(
                'Interactions\\InteractionCreated',
                this.created.bind(this)
            );
        echo.private(
            `tasks.${this.id}.interactions`).listen(
                'Interactions\\InteractionUpdated',
                this.updated.bind(this)
            );
        echo.private(
            `tasks.${this.id}.interactions`).listen(
                'Interactions\\InteractionDeleted',
                this.deleted.bind(this)
            );
    }

    created (payload) {
        payload.interaction.highlight = true;
        this.rows.data = [payload.interaction].concat(this.rows.data);
    }

    updated (payload) {
        payload.interaction.highlight = true;
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.interaction.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data[index] = payload.interaction;
        }
    }

    deleted (payload) {
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.interaction.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data.splice(index, 1);
        }
    }

    destroy () {
        echo.leave(`tasks.${this.id}.interactions`);
    }
}
