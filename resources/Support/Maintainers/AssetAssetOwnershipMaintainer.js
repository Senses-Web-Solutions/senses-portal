import useEcho from '../useEcho';

const echo = useEcho();

export default class {
    constructor(data, id) {
        this.rows = data;
        this.id = id;
        echo.private(
            `assets.${this.id}.asset-ownerships`).listen(
                'AssetOwnerships\\AssetOwnershipCreated',
                this.created.bind(this)
            );
        echo.private(
            `assets.${this.id}.asset-ownerships`).listen(
                'AssetOwnerships\\AssetOwnershipUpdated',
                this.updated.bind(this)
            );
        echo.private(
            `assets.${this.id}.asset-ownerships`).listen(
                'AssetOwnerships\\AssetOwnershipDeleted',
                this.deleted.bind(this)
            );
    }

    created (payload) {
        payload.assetOwnership.highlight = true;
        this.rows.data = [payload.assetOwnership].concat(this.rows.data);
    }

    updated (payload) {
        payload.assetOwnership.highlight = true;
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.assetOwnership.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data[index] = payload.assetOwnership;
        }
    }

    deleted (payload) {
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.assetOwnership.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data.splice(index, 1);
        }
    }

    destroy () {
        echo.leave(`assets.${this.id}.asset-ownerships`);
    }
}
