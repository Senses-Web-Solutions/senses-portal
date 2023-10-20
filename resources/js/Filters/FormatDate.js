import { format, parseISO } from 'date-fns';
export default function (date, dateFormat = 'dd/MM/yyyy') {
    if (!date) {
        return '';
    }
    return format(parseISO(date), dateFormat);
}
