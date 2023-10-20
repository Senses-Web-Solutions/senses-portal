import useEcho from '../useEcho';

const echo = useEcho();

export default class {
    constructor(data, id) {
        this.rows = data;
        this.id = id;
        echo.private(
            `assets.${this.id}.asset-maintenances`).listen(
                'AssetMaintenances\\AssetMaintenanceCreated',
                this.created.bind(this)
            );
        echo.private(
            `assets.${this.id}.asset-maintenances`).listen(
                'AssetMaintenances\\AssetMaintenanceUpdated',
                this.updated.bind(this)
            );
        echo.private(
            `assets.${this.id}.asset-maintenances`).listen(
                'AssetMaintenances\\AssetMaintenanceDeleted',
                this.deleted.bind(this)
            );
    }

    created (payload) {
        payload.assetMaintenance.highlight = true;
        this.rows.data = [payload.assetMaintenance].concat(this.rows.data);
    }

    updated (payload) {
        payload.assetMaintenance.highlight = true;
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.assetMaintenance.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data[index] = payload.assetMaintenance;
        }
    }

    deleted (payload) {
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.assetMaintenance.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data.splice(index, 1);
        }
    }

    destroy () {
        echo.leave(`assets.${this.id}.asset-maintenances`);
    }
}
