import useEcho from '../useEcho';
import EventHub from '../EventHub';

const echo = useEcho();

export default class {
    constructor(data, id) {
        this.rows = data;
        this.id = id;
        echo.private(
            `absence-types.${this.id}.absences`).listen(
            'Absences\\AbsenceCreated',
            this.absenceCreated.bind(this)
        );
    }

    absenceCreated(payload) {
        payload.absence.highlight = true;
        if(this.rows === null) {
            EventHub.emit('absence-created');
            return;
        }
        this.rows.data = [payload.absence].concat(this.rows.data);
    }

    destroy() {
        echo.leave(`absence-types.${this.id}.absences`);
    }
}
