import { formatDistance, parseISO } from 'date-fns';
export default function (date, since, addSuffix=true) {
    if (!date) {
        return '';
    }

    const sinceDate = since ? parseISO(since) : new Date();

    return formatDistance(parseISO(date), sinceDate, {
        addSuffix: addSuffix,
    });
}
