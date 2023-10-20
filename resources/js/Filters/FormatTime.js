import { format, parseISO } from 'date-fns';
export default function (date, dateFormat = 'hh:mmaaa') {
    if (!date) {
        return '';
    }
    return format(parseISO(date), dateFormat);
}
