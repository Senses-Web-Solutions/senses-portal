import useEcho from '../useEcho';

const echo = useEcho();

export default class {
    constructor(data, id) {
        this.rows = data;
        this.id = id;
        echo.private(
            `qualifications.${this.id}.user-qualifications`).listen(
            'UserQualifications\\UserQualificationCreated',
            this.userQualificationCreated.bind(this)
        );
        echo.private(
            `qualifications.${this.id}.user-qualifications`).listen(
            'UserQualifications\\UserQualificationUpdated',
            this.userQualificationUpdated.bind(this)
        );
        echo.private(
            `qualifications.${this.id}.user-qualifications`).listen(
            'UserQualifications\\UserQualificationDeleted',
            this.userQualificationDeleted.bind(this)
        );
    }

    userQualificationCreated(payload) {
        payload.userQualification.highlight = true;
        this.rows.data = [payload.userQualification].concat(this.rows.data);
    }

    userQualificationUpdated(payload) {
        payload.userQualification.highlight = true;

        const index = this.rows.data.findIndex(
            (item) => item.id === payload.userQualification.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data[index] = payload.userQualification;
        }
    }

    userQualificationDeleted(payload) {
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.userQualification.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data.splice(index, 1);
        }
    }

    destroy() {
        echo.leave(`qualifications.${this.id}.user-qualifications`);
    }
}
