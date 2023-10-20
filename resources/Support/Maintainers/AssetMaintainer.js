// import useEcho from '../useEcho';
import { forIn } from 'lodash-es';
import useEcho from '../useEcho';

// const echo = useEcho();
const echo = useEcho();

export default class {
    constructor(data) {
        this.data = data;
        echo.private(`assets.${this.data.id}.main`).listen("Assets\\AssetUpdated", this.assetUpdate.bind(this));
        echo.private(
            `assets.${this.data.id}.comment-stats`).listen(
            'Comments\\AssetCommentStatsUpdated',
            this.commentStatsUpdate.bind(this)
        );
        echo.private(
            `assets.${this.data.id}.asset-ownership-stats`).listen(
            'AssetOwnerships\\AssetAssetOwnershipStatsUpdated',
            this.assetOwnershipStatsUpdate.bind(this)
        );
        echo.private(
            `assets.${this.data.id}.asset-maintenance-stats`).listen(
            'AssetMaintenances\\AssetAssetMaintenanceStatsUpdated',
            this.assetMaintenanceStatsUpdate.bind(this)
        );
        echo.private(
            `assets.${this.data.id}.unavailability-stats`).listen(
            'Unavailabilities\\AssetUnavailabilityStatsUpdated',
            this.unavailabilityStatsUpdate.bind(this)
        );
        echo.private(
            `assets.${this.data.id}.revenue-stats`).listen(
            'Revenues\\AssetRevenueStatsUpdated',
            this.revenueStatsUpdate.bind(this)
        );

        echo.private(
            `assets.${this.data.id}.file-stats`).listen(
            'Files\\AssetFileStatsUpdated',
            this.fileStatsUpdate.bind(this)
        );
    }

    commentStatsUpdate (payload) {
        this.data.comments_count = payload.commentsCount;
    }

    assetOwnershipStatsUpdate (payload) {
        this.data.current_asset_ownership = payload.currentAssetOwnership;
        this.data.asset_ownerships_count = payload.assetOwnershipsCount;
    }

    assetMaintenanceStatsUpdate (payload) {
        this.data.asset_maintenances_count = payload.assetMaintenancesCount;
    }

    unavailabilityStatsUpdate (payload) {
        this.data.unavailabilities_count = payload.unavailabilitiesCount;
    }

    assetUpdate (payload) {
        forIn(payload.asset, (value, key) => {
            this.data[key] = value;
        });
    }

    revenueStatsUpdate (payload) {
        this.data.costs_total = payload.costsTotal;
        this.data.earnings_total = payload.earningsTotal;
        this.data.costs_count = payload.costsCount;
        this.data.earnings_count = payload.earningsCount;
        this.data.invoiced_total = payload.invoicedTotal;
        this.data.uninvoiced_total = payload.uninvoicedTotal;
        this.data.profit_total = payload.profitTotal;
    }

    fileStatsUpdate (payload) {
        this.data.files_count = payload.filesCount;
    }

    destroy () {
        echo.leave(`assets.${this.data.id}.main`);
        echo.leave(`assets.${this.data.id}.comment-stats`);
        echo.leave(`assets.${this.data.id}.asset-ownership-stats`);
        echo.leave(`assets.${this.data.id}.asset-maintenance-stats`);
        echo.leave(`assets.${this.data.id}.unavailability-stats`);
        echo.leave(`assets.${this.data.id}.revenue-stats`);
        echo.leave(`assets.${this.data.id}.file-stats`);
    }
}
