import useEcho from '../useEcho';

const echo = useEcho();

export default class {
    constructor(data, id) {
        this.rows = data;
        this.id = id;
        echo.private(
            `qualification-types.${this.id}.qualifications`).listen(
            'Qualifications\\QualificationCreated',
            this.qualificationCreated.bind(this)
        );
        echo.private(
            `qualification-types.${this.id}.qualifications`).listen(
            'Qualifications\\QualificationUpdated',
            this.qualificationUpdated.bind(this)
        );
        echo.private(
            `qualification-types.${this.id}.qualifications`).listen(
            'Qualifications\\QualificationDeleted',
            this.qualificationDeleted.bind(this)
        );
    }

    qualificationCreated(payload) {
        payload.qualification.highlight = true;
        this.rows.data = [payload.qualification].concat(this.rows.data);
    }

    qualificationUpdated(payload) {
        payload.qualification.highlight = true;

        const index = this.rows.data.findIndex(
            (item) => item.id === payload.qualification.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data[index] = payload.qualification;
        }
    }

    qualificationDeleted(payload) {
        const index = this.rows.data.findIndex(
            (item) => item.id === payload.qualification.id
        );
        if (index > -1 && this.rows.data[index]) {
            this.rows.data.splice(index, 1);
        }
    }

    destroy() {
        echo.leave(`qualification-types.${this.id}.qualifications`);
    }
}
