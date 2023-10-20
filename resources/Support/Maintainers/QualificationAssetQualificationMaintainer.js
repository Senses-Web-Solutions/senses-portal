import useEcho from '../useEcho';

const echo = useEcho();

export default class {
    constructor(data, id) {
        this.rows = data;
        this.id = id;
        echo.private(
            `qualifications.${this.id}.asset-qualifications`).listen(
            'AssetQualifications\\AssetQualificationCreated',
            this.assetQualificationCreated.bind(this)
        );
        echo.private(
            `qualifications.${this.id}.asset-qualifications`).listen(
            'AssetQualifications\\AssetQualificationUpdated',
            this.assetQualificationUpdated.bind(this)
        );
        echo.private(
            `qualifications.${this.id}.asset-qualifications`).listen(
            'AssetQualifications\\AssetQualificationDeleted',
            this.assetQualificationDeleted.bind(this)
        );
    }

    assetQualificationCreated(payload) {
        payload.assetQualification.highlight = true;
        this.rows.data = [payload.assetQualification].concat(this.rows.data);
    }

    assetQualificationUpdated(payload) {
        payload.assetQualification.highlight = true;

        const index = this.rows.data.findIndex(
            (item) => item.id === payload.assetQualification.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data[index] = payload.assetQualification;
        }
    }

    assetQualificationDeleted(payload) {
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.assetQualification.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data.splice(index, 1);
        }
    }

    destroy() {
        echo.leave(`qualifications.${this.id}.asset-qualifications`);
    }
}
