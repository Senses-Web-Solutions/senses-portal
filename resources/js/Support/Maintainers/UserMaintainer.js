import Maintainer from './Maintainer';

export default class extends Maintainer {
    constructor(data) {
        super('user', data);

        // echo.private(`users.${this.data.id}.holiday-stats`).listen(
        //     'Holidays\\UserHolidayStatsUpdated',
        //     this.holidaysStatsUpdate.bind(this)
        // );
        // echo.private(`users.${this.data.id}.absence-stats`).listen(
        //     'Absences\\UserAbsenceStatsUpdated',
        //     this.absenceStatsUpdate.bind(this)
        // );

    }

    holidaysStatsUpdate (payload) {
        //todo
    }

    absenceStatsUpdate (payload) {
        //todo
    }
}
