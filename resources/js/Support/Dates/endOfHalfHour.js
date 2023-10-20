import { millisecondsInMinute, startOfHour, addHours, toDate } from 'date-fns';

/**
 * @name endOfHalfHour
 * @category Hour Helpers
 * @summary Return the end of a half hour for the given date.
 *
 * @description
 * Return the end of an hour for the given date.
 * The result will be in the local timezone.
 *
 * @param date - the original date
 * @returns the end of an hour
 *
 * @example
 * // The end of an hour for 2 September 2014 11:55:00:
 * const result = endOfHalfHour(new Date(2014, 8, 2, 11, 55))
 * //=> Tue Sep 02 2014 12:00:00
 */
export default function endOfHalfHour(dirtyDate) {
    const date = toDate(dirtyDate);
    const dateEndOfHour = addHours(startOfHour(date), 1);

    const diff = dateEndOfHour - date;

    // If the difference is more than 30 minutes
    if (diff > (millisecondsInMinute * 30)) {
        return date.setMinutes(30, 0, 0);
    }

    return dateEndOfHour;
}
